<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Exercises\ListExercisesAction;
use App\Actions\Students\ListStudentsAction;
use App\Actions\Workouts\ListWorkoutsAction;
use App\Actions\Workouts\StoreWorkoutAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workouts\StoreWorkoutRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ExerciseResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\WorkoutResource;
use App\Models\Category;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class StoreWorkoutController extends Controller
{
    public function __invoke(
        StoreWorkoutRequest $request,
        StoreWorkoutAction $action,
        ListStudentsAction $studentsAction,
        ListExercisesAction $exercisesAction
    ): Response {
        $validated = $request->validated();

        $workout = $action->execute($validated);
        $workouts = (new ListWorkoutsAction)->execute(['trainer_id' => Auth::id()]);
        $students = $studentsAction->execute();
        $exercises = $exercisesAction->execute();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $student = Client::with('user')->find($validated['client_id']);

        return Inertia::render('workouts/ListWorkouts', [
            'title' => 'Treinos',
            'studentId' => $student?->id,
            'student' => $student ? new StudentResource($student) : null,
            'workouts' => WorkoutResource::collection($workouts),
            'students' => StudentResource::collection($students),
            'exercises' => ExerciseResource::collection($exercises),
            'categories' => CategoryResource::collection($categories),
        ])->with('success', 'Treino criado com sucesso');
    }
}
