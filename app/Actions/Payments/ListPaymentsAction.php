<?php

namespace App\Actions\Payments;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

class ListPaymentsAction
{
    public function execute(?string $status = null): Collection
    {
        $query = Payment::with(['client.user', 'plan']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->latest()->get();
    }
}
