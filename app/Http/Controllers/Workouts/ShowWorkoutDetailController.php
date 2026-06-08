<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Exercises\ListExercisesAction;
use App\Actions\Workouts\ListExerciseCustomWeightsAction;
use App\Actions\Workouts\StartWorkoutSessionAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\WorkoutResource;
use App\Models\Client;
use App\Models\Workout;
use App\Models\WorkoutSession;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShowWorkoutDetailController extends Controller
{
    public function __invoke(
        Workout $workout,
        ListExercisesAction $exercisesAction,
        StartWorkoutSessionAction $startSession,
        ListExerciseCustomWeightsAction $customWeightsAction,
        Request $request,
    ): Response {
        $this->authorize('view', $workout);

        $workout->loadMissing(['exercises.category', 'client.user', 'completions']);

        $client = Client::where('user_id', $request->user()->id)->first();

        $session = null;
        $customWeights = [];
        if ($client) {
            $session = WorkoutSession::where([
                'workout_id' => $workout->id,
                'client_id' => $client->id,
                'status' => 'in_progress',
            ])->first();

            if (! $session) {
                $session = $startSession->execute($workout, $client);
            }

            $workout->load('completions');

            $customWeights = $customWeightsAction->execute($workout->id, $client->id)
                ->mapWithKeys(fn ($cw) => [$cw->exercise_id => $cw->weight !== null ? (float) $cw->weight : null])
                ->toArray();
        }

        $exercises = $exercisesAction->execute();

        return Inertia::render('workouts/ShowWorkoutDetail', [
            'workout' => WorkoutResource::make($workout)->withSessionId($session?->id),
            'exercises' => $exercises,
            'session' => $session ? [
                'id' => $session->id,
                'started_at' => $session->started_at,
            ] : null,
            'customWeights' => $customWeights,
        ]);
    }
}
