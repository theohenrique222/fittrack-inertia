<?php

namespace App\Http\Controllers\Payments;

use App\Actions\Payments\ListPaymentsAction;
use App\Actions\Payments\MarkPaymentAsPaidAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\MarkPaymentAsPaidRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Inertia\Inertia;
use Inertia\Response;

class MarkPaymentAsPaidController extends Controller
{
    public function __invoke(
        MarkPaymentAsPaidRequest $request,
        Payment $payment,
        MarkPaymentAsPaidAction $action,
        ListPaymentsAction $listPayments,
    ): Response {
        $action->execute($payment, $request->validated());
        $payments = $listPayments->execute();

        return Inertia::render('payments/ListPayments', [
            'title' => 'Mensalidades',
            'payments' => PaymentResource::collection($payments),
        ])->with('success', 'Pagamento marcado como pago');
    }
}
