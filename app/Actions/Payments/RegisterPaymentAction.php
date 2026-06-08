<?php

namespace App\Actions\Payments;

use App\Models\Payment;

class RegisterPaymentAction
{
    public function execute(array $data): Payment
    {
        return Payment::create($data);
    }
}
