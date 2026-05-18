<?php

namespace App\Actions\Workouts;

use App\Models\Workout;

class DestroyWorkoutAction
{
    public function execute(Workout $workout): void
    {
        $workout->delete();
    }
}
