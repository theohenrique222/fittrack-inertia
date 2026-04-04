<?php

namespace App\Actions\Clients;

use App\Models\Client;
use Illuminate\Support\Facades\DB;

class UpdateClientAction
{
    public function execute(Client $client, array $data): Client
    {
        return DB::transaction(function () use ($client, $data) {
            $client->update([
                'nickname' => $data['nickname'] ?? $client->nickname,
            ]);

            $client->user()->update([
                'name' => $data['name'] ?? $client->user->name,
                'email' => $data['email'] ?? $client->user->email,
            ]);

            return $client->fresh(['user']);
        });
    }
}

