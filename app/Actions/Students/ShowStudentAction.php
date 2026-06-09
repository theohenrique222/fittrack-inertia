<?php

namespace App\Actions\Students;

use App\Actions\BodyMeasurements\BodyMetricsCalculator;
use App\Enums\PaymentStatus;
use App\Http\Resources\StudentResource;
use App\Http\Resources\WorkoutResource;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ShowStudentAction
{
    public function execute(Client $student): array
    {
        $student->loadMissing(['user', 'workouts.exercises.category', 'plan', 'payments.plan']);

        $activeWorkout = $student->workouts
            ->where('is_active', true)
            ->first();

        $totalWorkouts = $student->workouts()->count();
        $activeWorkoutsCount = $student->workouts()->where('is_active', true)->count();

        $latestMeasurement = $student->bodyMeasurements()
            ->latest('recorded_at')
            ->first();

        $latestMeasurementData = null;
        if ($latestMeasurement && $student->user->gender && $student->user->birthdate) {
            try {
                $calculator = new BodyMetricsCalculator($latestMeasurement, $student->user);
                $metrics = $calculator->calculate();
                $latestMeasurementData = [
                    'id' => $latestMeasurement->id,
                    'recorded_at' => $latestMeasurement->recorded_at->format('d/m/Y'),
                    'weight' => (float) $latestMeasurement->weight,
                    'height' => (float) $latestMeasurement->height,
                    'metrics' => $metrics,
                ];
            } catch (\RuntimeException) {
            }
        }

        $payments = $student->payments->sortByDesc('due_date');
        $totalPaid = $payments->where('status', PaymentStatus::PAID)->sum('amount');
        $totalPending = $payments->whereIn('status', [PaymentStatus::PENDING, PaymentStatus::OVERDUE])->sum('amount');
        $nextDuePayment = $payments
            ->whereIn('status', [PaymentStatus::PENDING, PaymentStatus::OVERDUE])
            ->sortBy('due_date')
            ->first();

        return [
            'student' => new StudentResource($student),
            'workout' => $activeWorkout ? new WorkoutResource($activeWorkout) : null,
            'workouts' => WorkoutResource::collection($student->workouts->sortByDesc('created_at')),
            'stats' => [
                'total_workouts' => $totalWorkouts,
                'active_workouts' => $activeWorkoutsCount,
                'created_at' => $student->created_at?->format('d/m/Y'),
                'trainer_name' => Auth::user()->name,
                'latest_measurement' => $latestMeasurementData,
            ],
            'financial' => [
                'plan' => $student->plan ? [
                    'id' => $student->plan->id,
                    'name' => $student->plan->name,
                    'price' => (float) $student->plan->price,
                ] : null,
                'next_due_date' => $nextDuePayment?->due_date?->format('d/m/Y'),
                'next_due_amount' => $nextDuePayment ? (float) $nextDuePayment->amount : 0,
                'total_paid' => (float) $totalPaid,
                'total_pending' => (float) $totalPending,
                'payments' => $payments->map(fn ($p) => [
                    'id' => $p->id,
                    'amount' => (float) $p->amount,
                    'due_date' => $p->due_date->format('d/m/Y'),
                    'paid_at' => $p->paid_at?->format('d/m/Y'),
                    'payment_method' => $p->payment_method,
                    'status' => $p->status->value,
                    'plan_name' => $p->plan?->name,
                    'notes' => $p->notes,
                ])->values()->toArray(),
            ],
        ];
    }
}
