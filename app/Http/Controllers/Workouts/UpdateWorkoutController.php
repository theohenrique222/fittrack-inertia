<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Exercises\ListExercisesAction;
use App\Actions\Students\ListStudentsAction;
use App\Actions\Workouts\ListWorkoutsAction;
use App\Actions\Workouts\UpdateWorkoutAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workouts\UpdateWorkoutRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ExerciseResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\WorkoutResource;
use App\Models\Category;
use App\Models\Client;
use App\Models\Workout;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class UpdateWorkoutController extends Controller
{
    public function __invoke(
        UpdateWorkoutRequest $request,
        Workout $workout,
        UpdateWorkoutAction $action,
        ListStudentsAction $studentsAction,
        ListExercisesAction $exercisesAction
    ): Response {
        $validated = $request->validated();

        $action->execute($workout, $validated);
        $workouts = (new ListWorkoutsAction)->execute(['trainer_id' => Auth::id()]);
        $students = $studentsAction->execute();
        $exercises = $exercisesAction->execute();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $client = Client::with('user')->find($validated['client_id'] ?? $workout->client_id);

        return Inertia::render('workouts/ListWorkouts', [
            'title' => 'Treinos',
            'clientId' => $client?->id,
            'student' => $client ? new StudentResource($client) : null,
            'workouts' => WorkoutResource::collection($workouts),
            'students' => StudentResource::collection($students),
            'exercises' => ExerciseResource::collection($exercises),
            'categories' => CategoryResource::collection($categories),
        ])->with('success', 'Treino atualizado com sucesso');
    }
}
