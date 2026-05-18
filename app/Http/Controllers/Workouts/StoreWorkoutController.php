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

        $action->execute($validated);
        $workouts = (new ListWorkoutsAction)->execute();
        $students = $studentsAction->execute();
        $exercises = $exercisesAction->execute();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('workouts/ListWorkouts', [
            'title' => 'Treinos',
            'workouts' => WorkoutResource::collection($workouts),
            'students' => StudentResource::collection($students),
            'exercises' => ExerciseResource::collection($exercises),
            'categories' => CategoryResource::collection($categories),
        ])->with('success', 'Treino criado com sucesso');
    }
}
