<?php

namespace App\Actions\Workouts;

use App\Models\Exercise;
use App\Models\ExerciseCompletion;
use App\Models\User;
use App\Models\Workout;

readonly class ToggleExerciseCompletionAction
{
    public function execute(Workout $workout, Exercise $exercise, User $user, ?int $sessionId = null): array
    {
        $existing = ExerciseCompletion::where([
            'workout_id' => $workout->id,
            'exercise_id' => $exercise->id,
            'user_id' => $user->id,
            'workout_session_id' => $sessionId,
        ])->first();

        if ($existing) {
            $existing->delete();

            return [
                'completed' => false,
                'message' => 'Exercício marcado como não concluído.',
            ];
        }

        ExerciseCompletion::create([
            'workout_id' => $workout->id,
            'exercise_id' => $exercise->id,
            'user_id' => $user->id,
            'workout_session_id' => $sessionId,
            'completed_at' => now(),
        ]);

        return [
            'completed' => true,
            'message' => 'Exercício concluído!',
        ];
    }
}
