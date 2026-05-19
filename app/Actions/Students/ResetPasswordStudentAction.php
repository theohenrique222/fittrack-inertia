<?php

namespace App\Actions\Students;

use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class ResetPasswordStudentAction
{
    public function execute(Client $student): void
    {
        $student->user()->update([
            'password' => Hash::make('password'),
        ]);
    }
}
