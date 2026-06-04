<?php

namespace App\Actions\Workouts;

use App\Models\ExerciseCompletion;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutSession;

readonly class CompleteWorkoutAction
{
    public function execute(Workout $workout, User $user, WorkoutSession $session): array
    {
        if ($session->status === 'completed') {
            return [
                'success' => false,
                'message' => 'Esta execução do treino já foi finalizada.',
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
            'workout_session_id' => $session->id,
        ])->count();

        if ($completedCount < $totalExercises) {
            return [
                'success' => false,
                'message' => 'Complete todos os exercícios antes de finalizar o treino. ('.$completedCount.' de '.$totalExercises.')',
            ];
        }

        $now = now();
        $startedAt = $session->started_at ?? $now;
        $durationMinutes = (int) $startedAt->diffInMinutes($now);

        $session->update([
            'completed_at' => $now,
            'duration_minutes' => $durationMinutes,
            'status' => 'completed',
        ]);

        return [
            'success' => true,
            'message' => 'Treino finalizado com sucesso!',
        ];
    }
}
