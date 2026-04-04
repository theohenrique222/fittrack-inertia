<?php

namespace App\Actions\Trainers;

use App\Models\Trainer;
use Illuminate\Support\Facades\DB;

class DestroyTrainerAction
{
    public function execute(Trainer $trainer): void
    {
        DB::transaction(function () use ($trainer) {
            $trainer->user()->delete();
            $trainer->delete();
        });
    }
}

