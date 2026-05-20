<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Workouts\UpdateWorkoutAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workouts\UpdateWorkoutRequest;
use App\Models\Workout;

class UpdateWorkoutController extends Controller
{
    public function __invoke(
        UpdateWorkoutRequest $request,
        Workout $workout,
        UpdateWorkoutAction $action,
    ) {
        $validated = $request->validated();

        $action->execute($workout, $validated);

        return redirect()
            ->route('students.show', $workout->client_id)
            ->with('success', 'Treino atualizado com sucesso');
    }
}
