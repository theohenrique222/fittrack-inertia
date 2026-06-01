<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Workouts\CompleteWorkoutAction;
use App\Http\Controllers\Controller;
use App\Models\Workout;
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

        $result = $action->execute($workout, $user);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
}
