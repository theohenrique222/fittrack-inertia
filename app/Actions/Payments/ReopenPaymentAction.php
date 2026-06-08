<?php

namespace App\Actions\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;

class ReopenPaymentAction
{
    public function execute(Payment $payment, ?array $data = null): Payment
    {
        $payment->update([
            'status' => PaymentStatus::PENDING,
            'paid_at' => null,
            'payment_method' => $data['payment_method'] ?? $payment->payment_method,
            'notes' => $data['notes'] ?? $payment->notes,
        ]);

        return $payment;
    }
}
