<?php

namespace App\Actions\Trainers;

use App\Models\Trainer;
use Illuminate\Support\Facades\DB;

class UpdateTrainerAction
{
    public function execute(Trainer $trainer, array $data): Trainer
    {
        return DB::transaction(function () use ($trainer, $data) {
            $trainer->update([
                'specialty' => $data['specialty'] ?? $trainer->specialty,
            ]);

            $trainer->user()->update([
                'name' => $data['name'] ?? $trainer->user->name,
                'email' => $data['email'] ?? $trainer->user->email,
            ]);

            return $trainer->fresh(['user']);
        });
    }
}

