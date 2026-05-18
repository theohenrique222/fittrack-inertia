<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Clients\ListClientsAction;
use App\Actions\Exercises\ListExercisesAction;
use App\Actions\Workouts\ListWorkoutsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ExerciseResource;
use App\Http\Resources\WorkoutResource;
use Inertia\Inertia;
use Inertia\Response;

class ListWorkoutsController extends Controller
{
    public function __invoke(
        ListWorkoutsAction $action,
        ListClientsAction $clientsAction,
        ListExercisesAction $exercisesAction
    ): Response {
        $filters = request()->only(['search', 'client_id', 'is_active']);
        $workouts = $action->execute($filters);
        $clients = $clientsAction->execute();
        $exercises = $exercisesAction->execute();

        return Inertia::render('workouts/ListWorkouts', [
            'title' => 'Treinos',
            'workouts' => WorkoutResource::collection($workouts),
            'clients' => ClientResource::collection($clients),
            'exercises' => ExerciseResource::collection($exercises),
        ]);
    }
}
