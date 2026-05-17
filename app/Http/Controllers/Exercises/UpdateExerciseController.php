<?php

namespace App\Http\Controllers\Exercises;

use App\Actions\Exercises\ListExercisesAction;
use App\Actions\Exercises\UpdateExerciseAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exercises\UpdateExerciseRequest;
use App\Http\Resources\ExerciseResource;
use App\Models\Exercise;
use Inertia\Inertia;
use Inertia\Response;

class UpdateExerciseController extends Controller
{
    public function __invoke(
        UpdateExerciseRequest $request,
        Exercise $exercise,
        UpdateExerciseAction $action
    ): Response {
        $validated = $request->validated();

        $action->execute($exercise, $validated);
        $exercises = (new ListExercisesAction)->execute();

        return Inertia::render('exercises/ListExercises', [
            'title' => 'Exercícios',
            'exercises' => ExerciseResource::collection($exercises),
        ])->with('success', 'Exercício atualizado com sucesso');
    }
}
