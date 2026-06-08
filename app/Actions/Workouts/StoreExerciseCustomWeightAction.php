<?php

namespace App\Actions\Workouts;

use App\Models\ExerciseCustomWeight;

class StoreExerciseCustomWeightAction
{
    public function execute(int $workoutId, int $exerciseId, int $clientId, ?float $weight): ExerciseCustomWeight
    {
        return ExerciseCustomWeight::updateOrCreate(
            [
                'workout_id' => $workoutId,
                'exercise_id' => $exerciseId,
                'client_id' => $clientId,
            ],
            ['weight' => $weight],
        );
    }
}
