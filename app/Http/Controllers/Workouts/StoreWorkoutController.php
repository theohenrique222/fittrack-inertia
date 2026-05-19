<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Workouts\StoreWorkoutAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workouts\StoreWorkoutRequest;

class StoreWorkoutController extends Controller
{
    public function __invoke(
        StoreWorkoutRequest $request,
        StoreWorkoutAction $action,
    ) {
        $validated = $request->validated();
        logger()->info('StoreWorkoutController called', $validated);

        $action->execute($validated);

        return redirect()
            ->route('students.show', $validated['client_id'])
            ->with('success', 'Treino criado com sucesso');
    }
}
