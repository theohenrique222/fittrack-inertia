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
        $this->authorize('view', $workout);

        $user = $request->user();

        $sessionId = $request->input('session_id');

        $result = $action->execute($workout, $exercise, $user, $sessionId);

        if ($result['completed']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('success', $result['message']);
    }
}
