<?php

namespace App\Http\Controllers\Payments;

use App\Actions\Payments\ListPaymentsAction;
use App\Actions\Payments\RegisterPaymentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use Inertia\Inertia;
use Inertia\Response;

class StorePaymentController extends Controller
{
    public function __invoke(
        StorePaymentRequest $request,
        RegisterPaymentAction $action,
        ListPaymentsAction $listPayments,
    ): Response {
        $validated = $request->validated();

        $action->execute($validated);
        $payments = $listPayments->execute();

        return Inertia::render('payments/ListPayments', [
            'title' => 'Mensalidades',
            'payments' => PaymentResource::collection($payments),
        ])->with('success', 'Pagamento registrado com sucesso');
    }
}
