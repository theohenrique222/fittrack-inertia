<?php

namespace App\Actions\Financial;

use App\Enums\PaymentStatus;
use App\Models\Client;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class GetFinancialDashboardAction
{
    public function execute(
        User $user,
        ?string $period = '30d',
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
        ?int $studentId = null,
        ?string $status = null,
        ?string $paymentMethod = null,
    ): array {
        $dateRange = $this->resolveDateRange($period, $startDate, $endDate);

        $query = Payment::with(['client.user', 'plan'])
            ->when($user->isPersonal(), fn (Builder $q) => $q->whereHas('client.user', fn (Builder $q) => $q->where('trainer_id', $user->id)))
            ->when($dateRange, fn (Builder $q) => $q->whereBetween('due_date', [$dateRange['start'], $dateRange['end']]))
            ->when($studentId, fn (Builder $q) => $q->where('client_id', $studentId))
            ->when($status, fn (Builder $q) => $q->where('status', $status))
            ->when($paymentMethod, fn (Builder $q) => $q->where('payment_method', $paymentMethod));

        $allPayments = (clone $query)->latest()->get();

        $studentsQuery = Client::with('user')
            ->when($user->isPersonal(), fn (Builder $q) => $q->whereHas('user', fn (Builder $q) => $q->where('trainer_id', $user->id)));

        $students = $studentsQuery->get();

        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $monthPayments = Payment::with(['client.user', 'plan'])
            ->when($user->isPersonal(), fn (Builder $q) => $q->whereHas('client.user', fn (Builder $q) => $q->where('trainer_id', $user->id)))
            ->whereBetween('due_date', [$monthStart, $monthEnd])
            ->get();

        $monthRevenue = $monthPayments->where('status', PaymentStatus::PAID)->sum('amount');
        $monthPending = $monthPayments->whereIn('status', [PaymentStatus::PENDING, PaymentStatus::OVERDUE])->sum('amount');
        $monthPredictedRevenue = $monthRevenue + $monthPending;

        $allPaid = $allPayments->where('status', PaymentStatus::PAID)->sum('amount');
        $allPending = $allPayments->whereIn('status', [PaymentStatus::PENDING, PaymentStatus::OVERDUE])->sum('amount');
        $allOverdue = $allPayments->where('status', PaymentStatus::OVERDUE)->sum('amount');

        $overdueStudentsCount = Payment::where('status', PaymentStatus::OVERDUE)
            ->when($user->isPersonal(), fn (Builder $q) => $q->whereHas('client.user', fn (Builder $q) => $q->where('trainer_id', $user->id)))
            ->distinct('client_id')
            ->count('client_id');

        $latestPayments = $allPayments
            ->where('status', PaymentStatus::PAID)
            ->sortByDesc('paid_at')
            ->take(10)
            ->values()
            ->map(fn ($p) => $this->formatPayment($p));

        $upcomingDues = $allPayments
            ->where('status', PaymentStatus::PENDING)
            ->sortBy('due_date')
            ->take(10)
            ->values()
            ->map(fn ($p) => $this->formatPayment($p));

        $overduePayments = $allPayments
            ->where('status', PaymentStatus::OVERDUE)
            ->sortByDesc('due_date')
            ->take(10)
            ->values()
            ->map(fn ($p) => $this->formatPayment($p));

        $filteredTotalReceived = $allPayments->where('status', PaymentStatus::PAID)->sum('amount');
        $filteredTotalPending = $allPayments->whereIn('status', [PaymentStatus::PENDING, PaymentStatus::OVERDUE])->sum('amount');
        $filteredTotalOverdue = $allPayments->where('status', PaymentStatus::OVERDUE)->sum('amount');
        $filteredPaymentCount = $allPayments->count();

        return [
            'stats' => [
                'month_revenue' => (float) $monthRevenue,
                'month_pending' => (float) $monthPending,
                'month_predicted_revenue' => (float) $monthPredictedRevenue,
                'total_received' => (float) $allPaid,
                'total_pending' => (float) $allPending,
                'total_overdue' => (float) $allOverdue,
                'overdue_students_count' => $overdueStudentsCount,
            ],
            'filtered_totals' => [
                'total_received' => (float) $filteredTotalReceived,
                'total_pending' => (float) $filteredTotalPending,
                'total_overdue' => (float) $filteredTotalOverdue,
                'payment_count' => $filteredPaymentCount,
            ],
            'latest_payments' => $latestPayments,
            'upcoming_dues' => $upcomingDues,
            'overdue_payments' => $overduePayments,
            'students' => $students->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->user?->name ?? 'Sem nome',
            ])->values()->toArray(),
            'selected_period' => $period,
        ];
    }

    /**
     * @return array{start: Carbon, end: Carbon}|null
     */
    private function resolveDateRange(?string $period, ?Carbon $startDate, ?Carbon $endDate): ?array
    {
        if ($startDate && $endDate) {
            return ['start' => $startDate, 'end' => $endDate];
        }

        return match ($period) {
            '7d' => ['start' => Carbon::now()->subDays(7), 'end' => Carbon::now()],
            '30d' => ['start' => Carbon::now()->subDays(30), 'end' => Carbon::now()],
            '90d' => ['start' => Carbon::now()->subDays(90), 'end' => Carbon::now()],
            '12m' => ['start' => Carbon::now()->subMonths(12), 'end' => Carbon::now()],
            default => null,
        };
    }

    private function formatPayment(Payment $payment): array
    {
        return [
            'id' => $payment->id,
            'client_id' => $payment->client_id,
            'amount' => (float) $payment->amount,
            'due_date' => $payment->due_date->format('d/m/Y'),
            'paid_at' => $payment->paid_at?->format('d/m/Y'),
            'payment_method' => $payment->payment_method,
            'status' => $payment->status->value,
            'plan_name' => $payment->plan?->name,
            'client_name' => $payment->client?->user?->name ?? $payment->client?->nickname ?? '---',
            'client_nickname' => $payment->client?->nickname,
        ];
    }
}
