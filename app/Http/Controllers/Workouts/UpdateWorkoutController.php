<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Clients\ListClientsAction;
use App\Actions\Exercises\ListExercisesAction;
use App\Actions\Workouts\ListWorkoutsAction;
use App\Actions\Workouts\UpdateWorkoutAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workouts\UpdateWorkoutRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ExerciseResource;
use App\Http\Resources\WorkoutResource;
use App\Models\Workout;
use Inertia\Inertia;
use Inertia\Response;

class UpdateWorkoutController extends Controller
{
    public function __invoke(
        UpdateWorkoutRequest $request,
        Workout $workout,
        UpdateWorkoutAction $action,
        ListClientsAction $clientsAction,
        ListExercisesAction $exercisesAction
    ): Response {
        $validated = $request->validated();

        $action->execute($workout, $validated);
        $workouts = (new ListWorkoutsAction)->execute();
        $clients = $clientsAction->execute();
        $exercises = $exercisesAction->execute();

        return Inertia::render('workouts/ListWorkouts', [
            'title' => 'Treinos',
            'workouts' => WorkoutResource::collection($workouts),
            'clients' => ClientResource::collection($clients),
            'exercises' => ExerciseResource::collection($exercises),
        ])->with('success', 'Treino atualizado com sucesso');
    }
}
