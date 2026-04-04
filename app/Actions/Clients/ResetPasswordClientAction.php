<?php

namespace App\Actions\Clients;

use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class ResetPasswordClientAction
{
    public function execute(Client $client): void
    {
        $client->user()->update([
            'password' => Hash::make('password'),
        ]);
    }
}

