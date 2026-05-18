<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Clients\ListClientsAction;
use App\Actions\Exercises\ListExercisesAction;
use App\Actions\Workouts\GenerateWorkoutAction;
use App\Actions\Workouts\ListWorkoutsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workouts\GenerateWorkoutRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ExerciseResource;
use App\Http\Resources\WorkoutResource;
use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class GenerateWorkoutController extends Controller
{
    public function __invoke(
        GenerateWorkoutRequest $request,
        GenerateWorkoutAction $action,
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
        ])->with('success', 'Treino gerado automaticamente com sucesso');
    }
}
