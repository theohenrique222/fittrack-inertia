<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Clients\ListClientsAction;
use App\Actions\Exercises\ListExercisesAction;
use App\Actions\Workouts\DestroyWorkoutAction;
use App\Actions\Workouts\ListWorkoutsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ExerciseResource;
use App\Http\Resources\WorkoutResource;
use App\Models\Workout;
use Inertia\Inertia;
use Inertia\Response;

class DestroyWorkoutController extends Controller
{
    public function __invoke(
        Workout $workout,
        DestroyWorkoutAction $action,
        ListClientsAction $clientsAction,
        ListExercisesAction $exercisesAction
    ): Response {
        $action->execute($workout);
        $workouts = (new ListWorkoutsAction)->execute();
        $clients = $clientsAction->execute();
        $exercises = $exercisesAction->execute();

        return Inertia::render('workouts/ListWorkouts', [
            'title' => 'Treinos',
            'workouts' => WorkoutResource::collection($workouts),
            'clients' => ClientResource::collection($clients),
            'exercises' => ExerciseResource::collection($exercises),
        ])->with('success', 'Treino deletado com sucesso');
    }
}
