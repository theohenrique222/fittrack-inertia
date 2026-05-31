<?php

namespace App\Actions\Workouts;

use App\Models\Exercise;
use App\Models\ExerciseCompletion;
use App\Models\User;
use App\Models\Workout;

readonly class ToggleExerciseCompletionAction
{
    public function execute(Workout $workout, Exercise $exercise, User $user): array
    {
        $completion = ExerciseCompletion::where([
            'workout_id' => $workout->id,
            'exercise_id' => $exercise->id,
            'user_id' => $user->id,
        ])->first();

        if ($completion) {
            $completion->delete();
            $completed = false;
        } else {
            ExerciseCompletion::create([
                'workout_id' => $workout->id,
                'exercise_id' => $exercise->id,
                'user_id' => $user->id,
                'completed_at' => now(),
            ]);
            $completed = true;
        }

        return [
            'completed' => $completed,
        ];
    }
}
