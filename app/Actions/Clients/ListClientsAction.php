<?php

namespace App\Actions\Clients;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;

class ListClientsAction
{
    public function execute(): Collection
    {
        return Client::with('user')->get();
    }
}

