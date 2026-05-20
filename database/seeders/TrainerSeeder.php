<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TrainerSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Treinador',
            'email' => 'treinador@email.com',
            'password' => Hash::make('password'),
            'role' => UserRole::PERSONAL,
            'email_verified_at' => now(),
        ]);

        Client::create([
            'user_id' => $user->id,
        ]);
    }
}
