<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Clients\ListClientsAction;
use App\Actions\Exercises\ListExercisesAction;
use App\Actions\Workouts\ListWorkoutsAction;
use App\Actions\Workouts\StoreWorkoutAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workouts\StoreWorkoutRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ExerciseResource;
use App\Http\Resources\WorkoutResource;
use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class StoreWorkoutController extends Controller
{
    public function __invoke(
        StoreWorkoutRequest $request,
        StoreWorkoutAction $action,
        ListClientsAction $clientsAction,
        ListExercisesAction $exercisesAction
    ): Response {
        $validated = $request->validated();

        $action->execute($validated);
        $workouts = (new ListWorkoutsAction)->execute();
        $clients = $clientsAction->execute();
        $exercises = $exercisesAction->execute();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('workouts/ListWorkouts', [
            'title' => 'Treinos',
            'workouts' => WorkoutResource::collection($workouts),
            'clients' => ClientResource::collection($clients),
            'exercises' => ExerciseResource::collection($exercises),
            'categories' => CategoryResource::collection($categories),
        ])->with('success', 'Treino criado com sucesso');
    }
}
