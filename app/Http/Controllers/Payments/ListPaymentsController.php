<?php

namespace App\Http\Controllers\Payments;

use App\Actions\Payments\ListOverduePaymentsAction;
use App\Actions\Payments\ListPaymentsAction;
use App\Actions\Payments\ListPendingPaymentsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Client;
use App\Models\Plan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListPaymentsController extends Controller
{
    public function __invoke(
        Request $request,
        ListPaymentsAction $listPayments,
        ListPendingPaymentsAction $listPending,
        ListOverduePaymentsAction $listOverdue,
    ): Response {
        $filter = $request->query('filter');

        $payments = match ($filter) {
            'pending' => $listPending->execute(),
            'overdue' => $listOverdue->execute(),
            default => $listPayments->execute(),
        };

        $students = Client::with('user')->get();
        $plans = Plan::where('is_active', true)->get();

        return Inertia::render('payments/ListPayments', [
            'title' => 'Mensalidades',
            'payments' => PaymentResource::collection($payments),
            'filter' => $filter,
            'students' => $students,
            'plans' => $plans,
        ]);
    }
}
