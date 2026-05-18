<?php

namespace App\Actions\Students;

use App\Http\Resources\StudentResource;
use App\Http\Resources\WorkoutResource;
use App\Models\Client;

class ShowStudentAction
{
    public function execute(Client $student): array
    {
        $student->load(['user', 'workouts.exercises.category']);

        $activeWorkout = $student->workouts
            ->where('is_active', true)
            ->first();

        return [
            'student' => new StudentResource($student),
            'workout' => $activeWorkout ? new WorkoutResource($activeWorkout) : null,
        ];
    }
}
