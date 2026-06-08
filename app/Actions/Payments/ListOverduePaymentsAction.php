<?php

namespace App\Actions\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

class ListOverduePaymentsAction
{
    public function execute(): Collection
    {
        return Payment::with(['client.user', 'plan'])
            ->where('status', PaymentStatus::OVERDUE)
            ->latest()
            ->get();
    }
}
