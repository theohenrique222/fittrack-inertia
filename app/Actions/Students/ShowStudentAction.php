<?php

namespace App\Actions\Students;

use App\Http\Resources\StudentResource;
use App\Http\Resources\WorkoutResource;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ShowStudentAction
{
    public function execute(Client $student): array
    {
        $student->loadMissing(['user', 'workouts.exercises.category']);

        $activeWorkout = $student->workouts
            ->where('is_active', true)
            ->first();

        $totalWorkouts = $student->workouts()->count();
        $activeWorkoutsCount = $student->workouts()->where('is_active', true)->count();

        return [
            'student' => new StudentResource($student),
            'workout' => $activeWorkout ? new WorkoutResource($activeWorkout) : null,
            'workouts' => WorkoutResource::collection($student->workouts->sortByDesc('created_at')),
            'stats' => [
                'total_workouts' => $totalWorkouts,
                'active_workouts' => $activeWorkoutsCount,
                'created_at' => $student->created_at?->format('d/m/Y'),
                'trainer_name' => Auth::user()->name,
            ],
        ];
    }
}
