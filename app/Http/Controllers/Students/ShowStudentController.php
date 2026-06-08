<?php

namespace App\Http\Controllers\Students;

use App\Actions\Exercises\ListExercisesAction;
use App\Actions\Plans\ListPlansAction;
use App\Actions\Students\ShowStudentAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ExerciseResource;
use App\Http\Resources\PlanResource;
use App\Models\Category;
use App\Models\Client;
use Inertia\Inertia;
use Inertia\Response;

class ShowStudentController extends Controller
{
    public function __invoke(
        Client $student,
        ShowStudentAction $action,
        ListExercisesAction $exercisesAction,
        ListPlansAction $plansAction,
    ): Response {
        $data = $action->execute($student);
        $exercises = $exercisesAction->execute();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $plans = $plansAction->execute(['is_active' => true]);

        return Inertia::render('students/ShowStudent', [
            'title' => 'Aluno - '.$data['student']['name'],
            'student' => $data['student'],
            'workout' => $data['workout'],
            'workouts' => $data['workouts'],
            'stats' => $data['stats'],
            'exercises' => ExerciseResource::collection($exercises),
            'categories' => CategoryResource::collection($categories),
            'plans' => PlanResource::collection($plans),
        ]);
    }
}
