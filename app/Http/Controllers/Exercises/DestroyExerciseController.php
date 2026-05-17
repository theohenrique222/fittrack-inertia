<?php

namespace App\Http\Controllers\Exercises;

use App\Actions\Exercises\DestroyExerciseAction;
use App\Actions\Exercises\ListExercisesAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExerciseResource;
use App\Models\Exercise;
use Inertia\Inertia;
use Inertia\Response;

class DestroyExerciseController extends Controller
{
    public function __invoke(
        Exercise $exercise,
        DestroyExerciseAction $action
    ): Response {
        $action->execute($exercise);
        $exercises = (new ListExercisesAction)->execute();

        return Inertia::render('exercises/ListExercises', [
            'title' => 'Exercícios',
            'exercises' => ExerciseResource::collection($exercises),
        ])->with('success', 'Exercício deletado com sucesso');
    }
}
