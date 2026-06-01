<?php

namespace App\Actions\Workouts;

use App\Models\ExerciseCompletion;
use App\Models\User;
use App\Models\Workout;

readonly class CompleteWorkoutAction
{
    public function execute(Workout $workout, User $user): array
    {
        if ($workout->completed_at) {
            return [
                'success' => false,
                'message' => 'Este treino já foi finalizado.',
            ];
        }

        $totalExercises = $workout->exercises()->count();

        if ($totalExercises === 0) {
            return [
                'success' => false,
                'message' => 'Este treino não possui exercícios.',
            ];
        }

        $completedCount = ExerciseCompletion::where([
            'workout_id' => $workout->id,
            'user_id' => $user->id,
        ])->count();

        if ($completedCount < $totalExercises) {
            return [
                'success' => false,
                'message' => 'Complete todos os exercícios antes de finalizar o treino. ('.$completedCount.' de '.$totalExercises.')',
            ];
        }

        $workout->update(['completed_at' => now()]);

        return [
            'success' => true,
            'message' => 'Treino finalizado com sucesso!',
        ];
    }
}
