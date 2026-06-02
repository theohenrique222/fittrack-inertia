<?php

namespace App\Actions\Trainers;

use App\Enums\UserRole;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StoreTrainerAction
{
    public function execute(array $data): Trainer
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => UserRole::PERSONAL,
                'email_verified_at' => now(),
                'must_reset_password' => true,
            ]);

            return Trainer::create([
                'user_id' => $user->id,
                'specialty' => $data['specialty'] ?? null,
            ]);
        });
    }
}
