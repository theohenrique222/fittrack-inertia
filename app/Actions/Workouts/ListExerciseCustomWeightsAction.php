<?php

namespace App\Actions\Workouts;

use App\Models\ExerciseCustomWeight;
use Illuminate\Database\Eloquent\Collection;

class ListExerciseCustomWeightsAction
{
    /**
     * @return Collection<int, ExerciseCustomWeight>
     */
    public function execute(int $workoutId, int $clientId): Collection
    {
        return ExerciseCustomWeight::where('workout_id', $workoutId)
            ->where('client_id', $clientId)
            ->get();
    }
}
