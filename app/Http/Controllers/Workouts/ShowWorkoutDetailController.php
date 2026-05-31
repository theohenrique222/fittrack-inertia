<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Exercises\ListExercisesAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\WorkoutResource;
use App\Models\Workout;
use Inertia\Inertia;
use Inertia\Response;

class ShowWorkoutDetailController extends Controller
{
    public function __invoke(
        Workout $workout,
        ListExercisesAction $exercisesAction,
    ): Response {
        $workout->loadMissing(['exercises.category', 'client.user', 'completions']);

        $exercises = $exercisesAction->execute();

        return Inertia::render('workouts/ShowWorkoutDetail', [
            'workout' => new WorkoutResource($workout),
            'exercises' => $exercises,
        ]);
    }
}
