<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Exercises\ListExercisesAction;
use App\Actions\Workouts\ListWorkoutsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ExerciseResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\WorkoutResource;
use App\Models\Category;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ListWorkoutsController extends Controller
{
    public function __invoke(
        ListWorkoutsAction $action,
        ListExercisesAction $exercisesAction
    ): Response {
        $clientId = request()->query('client_id');

        if (! $clientId) {
            abort(403, 'Selecione um aluno para visualizar os treinos.');
        }

        $client = Client::with('user')->find($clientId);

        if (! $client) {
            abort(404, 'Aluno não encontrado.');
        }

        $filters = request()->only(['search', 'is_active']);
        $filters['trainer_id'] = Auth::id();
        $filters['client_id'] = $clientId;

        $workouts = $action->execute($filters);
        $exercises = $exercisesAction->execute();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('workouts/ListWorkouts', [
            'title' => 'Treinos',
            'clientId' => (int) $clientId,
            'student' => new StudentResource($client),
            'workouts' => WorkoutResource::collection($workouts),
            'exercises' => ExerciseResource::collection($exercises),
            'categories' => CategoryResource::collection($categories),
        ]);
    }
}
