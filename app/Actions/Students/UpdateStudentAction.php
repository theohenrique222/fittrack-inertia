<?php

namespace App\Actions\Students;

use App\Models\Client;
use Illuminate\Support\Facades\DB;

class UpdateStudentAction
{
    public function execute(Client $student, array $data): Client
    {
        return DB::transaction(function () use ($student, $data) {
            $student->update([
                'nickname' => $data['nickname'] ?? $student->nickname,
            ]);

            $student->user()->update([
                'name' => $data['name'] ?? $student->user->name,
                'email' => $data['email'] ?? $student->user->email,
            ]);

            return $student->fresh(['user']);
        });
    }
}
