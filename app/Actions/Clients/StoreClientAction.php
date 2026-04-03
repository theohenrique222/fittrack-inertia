<?php

namespace App\Actions\Clients;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StoreClientAction
{
    public function execute(array $data): Client
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?? 'password'),
                'role' => UserRole::CLIENT,
                'email_verified_at' => now(),
            ]);

            return Client::create([
                'user_id' => $user->id,
                'nickname' => $data['nickname'] ?? null,
            ]);
        });
    }
}
