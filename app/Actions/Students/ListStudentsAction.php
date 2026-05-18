<?php

namespace App\Actions\Students;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class ListStudentsAction
{
    public function execute(): Collection
    {
        return Client::with('user')
            ->whereHas('user', function ($query) {
                $query->where('trainer_id', Auth::id());
            })
            ->get();
    }
}
