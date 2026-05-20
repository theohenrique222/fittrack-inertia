<?php

namespace App\Actions\Students;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

class ListAllStudentsAction
{
    public function execute(): Collection
    {
        return Client::with(['user', 'user.trainer'])
            ->get();
    }
}
