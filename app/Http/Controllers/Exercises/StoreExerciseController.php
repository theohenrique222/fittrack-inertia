<?php

namespace App\Http\Controllers\Exercises;

use App\Actions\Categories\ListCategoriesAction;
use App\Actions\Exercises\ListExercisesAction;
use App\Actions\Exercises\StoreExerciseAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exercises\StoreExerciseRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ExerciseResource;
use Inertia\Inertia;
use Inertia\Response;

class StoreExerciseController extends Controller
{
    public function __invoke(
        StoreExerciseRequest $request,
        StoreExerciseAction $action,
        ListCategoriesAction $categoriesAction
    ): Response {
        $validated = $request->validated();

        $action->execute($validated);
        $exercises = (new ListExercisesAction)->execute();
        $categories = $categoriesAction->execute();

        return Inertia::render('exercises/ListExercises', [
            'title' => 'Exercícios',
            'exercises' => ExerciseResource::collection($exercises),
            'categories' => CategoryResource::collection($categories),
        ])->with('success', 'Exercício criado com sucesso');
    }
}
