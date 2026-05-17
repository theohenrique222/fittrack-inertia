<?php

namespace App\Http\Controllers\Exercises;

use App\Actions\Categories\ListCategoriesAction;
use App\Actions\Exercises\ListExercisesAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ExerciseResource;
use Inertia\Inertia;
use Inertia\Response;

class ListExercisesController extends Controller
{
    public function __invoke(
        ListExercisesAction $action,
        ListCategoriesAction $categoriesAction
    ): Response {
        $exercises = $action->execute();
        $categories = $categoriesAction->execute();

        return Inertia::render('exercises/ListExercises', [
            'title' => 'Exercícios',
            'exercises' => ExerciseResource::collection($exercises),
            'categories' => CategoryResource::collection($categories),
        ]);
    }
}
