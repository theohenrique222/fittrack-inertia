<?php

namespace App\Actions\Clients;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StoreClientAction
{
    public function execute(array $data): Client
    {
        return DB::transaction(function () use ($data) {
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = Hash::make($data['password'] ?? 'password');
            $user->role = UserRole::CLIENT->value;
            $user->trainer_id = Auth::id();
            $user->email_verified_at = now();
            $user->save();

            return Client::create([
                'user_id' => $user->id,
                'nickname' => $data['nickname'] ?? null,
            ]);
        });
    }
}
