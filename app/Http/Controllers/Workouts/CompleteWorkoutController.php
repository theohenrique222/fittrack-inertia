<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Workouts\CompleteWorkoutAction;
use App\Http\Controllers\Controller;
use App\Models\Workout;
use App\Models\WorkoutSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompleteWorkoutController extends Controller
{
    public function __invoke(
        Workout $workout,
        Request $request,
        CompleteWorkoutAction $action,
    ): RedirectResponse {
        $user = $request->user();

        $this->authorize('view', $workout);

        $sessionId = $request->input('session_id');

        $session = WorkoutSession::findOrFail($sessionId);

        $result = $action->execute($workout, $user, $session);

        if ($result['success']) {
            return redirect()->route('me.workouts')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
}
