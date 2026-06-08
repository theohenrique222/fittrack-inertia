<?php

namespace App\Actions\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

class ListPendingPaymentsAction
{
    public function execute(): Collection
    {
        return Payment::with(['client.user', 'plan'])
            ->where('status', PaymentStatus::PENDING)
            ->latest()
            ->get();
    }
}
