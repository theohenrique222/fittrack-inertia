<?php

namespace App\Actions\Trainers;

use App\Enums\UserRole;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StoreTrainerAction
{
    public function execute(array $data): Trainer
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'] ?? 'password',
                'role' => UserRole::PERSONAL,
                'email_verified_at' => now(),
            ]);

            return Trainer::create([
                'user_id' => $user->id,
                'specialty' => $data['specialty'] ?? null,
            ]);
        });
    }
}
