<?php

namespace App\Http\Controllers\Payments;

use App\Actions\Payments\ListPaymentsAction;
use App\Actions\Payments\ReopenPaymentAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Inertia\Inertia;
use Inertia\Response;

class ReopenPaymentController extends Controller
{
    public function __invoke(
        Payment $payment,
        ReopenPaymentAction $action,
        ListPaymentsAction $listPayments,
    ): Response {
        $action->execute($payment);
        $payments = $listPayments->execute();

        return Inertia::render('payments/ListPayments', [
            'title' => 'Mensalidades',
            'payments' => PaymentResource::collection($payments),
        ])->with('success', 'Pagamento reaberto para correção');
    }
}
