<?php

namespace App\Http\Controllers\Exercises;

use App\Actions\Exercises\ListExercisesAction;
use App\Actions\Exercises\StoreExerciseAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exercises\StoreExerciseRequest;
use App\Http\Resources\ExerciseResource;
use Inertia\Inertia;
use Inertia\Response;

class StoreExerciseController extends Controller
{
    public function __invoke(
        StoreExerciseRequest $request,
        StoreExerciseAction $action
    ): Response {
        $validated = $request->validated();

        $action->execute($validated);
        $exercises = (new ListExercisesAction)->execute();

        return Inertia::render('exercises/ListExercises', [
            'title' => 'Exercícios',
            'exercises' => ExerciseResource::collection($exercises),
        ])->with('success', 'Exercício criado com sucesso');
    }
}
