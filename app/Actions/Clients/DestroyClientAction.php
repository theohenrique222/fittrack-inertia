<?php

namespace App\Actions\Clients;

use App\Models\Client;
use Illuminate\Support\Facades\DB;

class DestroyClientAction
{
    public function execute(Client $client): void
    {
        DB::transaction(function () use ($client) {
            $client->user()->delete();
            $client->delete();
        });
    }
}

