<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Exercises\ListExercisesAction;
use App\Actions\Workouts\StartWorkoutSessionAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\WorkoutResource;
use App\Models\Client;
use App\Models\Workout;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShowWorkoutDetailController extends Controller
{
    public function __invoke(
        Workout $workout,
        ListExercisesAction $exercisesAction,
        StartWorkoutSessionAction $startSession,
        Request $request,
    ): Response {
        $this->authorize('view', $workout);

        $workout->loadMissing(['exercises.category', 'client.user', 'completions']);

        $client = Client::where('user_id', $request->user()->id)->first();

        $session = null;
        if ($client) {
            $session = $startSession->execute($workout, $client);
            $workout->load('completions');
        }

        $exercises = $exercisesAction->execute();

        return Inertia::render('workouts/ShowWorkoutDetail', [
            'workout' => WorkoutResource::make($workout)->withSessionId($session?->id),
            'exercises' => $exercises,
            'session' => $session ? [
                'id' => $session->id,
                'started_at' => $session->started_at,
            ] : null,
        ]);
    }
}
