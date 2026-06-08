<?php

namespace App\Actions\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;

class MarkPaymentAsPaidAction
{
    public function execute(Payment $payment, array $data): Payment
    {
        $payment->update([
            'status' => PaymentStatus::PAID,
            'paid_at' => $data['paid_at'] ?? now(),
            'payment_method' => $data['payment_method'] ?? $payment->payment_method,
        ]);

        return $payment;
    }
}
