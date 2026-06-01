<?php

namespace App\Actions\Trainers;

use App\Models\Trainer;
use Illuminate\Support\Facades\Hash;

class ResetPasswordTrainerAction
{
    public function execute(Trainer $trainer): void
    {
        $trainer->user()->update([
            'password' => Hash::make('password'),
            'must_reset_password' => true,
        ]);
    }
}
