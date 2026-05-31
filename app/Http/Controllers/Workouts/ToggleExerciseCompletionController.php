<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Workouts\ToggleExerciseCompletionAction;
use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ToggleExerciseCompletionController extends Controller
{
    public function __invoke(
        Workout $workout,
        Exercise $exercise,
        Request $request,
        ToggleExerciseCompletionAction $action,
    ): RedirectResponse {
        $user = $request->user();

        $this->authorize('view', $workout);

        $action->execute($workout, $exercise, $user);

        return redirect()->back();
    }
}
