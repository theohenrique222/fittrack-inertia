<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Workouts\ListWorkoutsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\WorkoutResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ListStudentWorkoutsController extends Controller
{
    public function __invoke(
        Request $request,
        ListWorkoutsAction $action,
    ): Response {
        $client = Client::where('user_id', $request->user()->id)->firstOrFail();

        $filters = request()->only(['search', 'is_active']);
        $filters['client_id'] = $client->id;

        $workouts = $action->execute($filters);
        $workouts->load('completions');

        return Inertia::render('workouts/StudentListWorkouts', [
            'workouts' => WorkoutResource::collection($workouts),
            'stats' => [
                'total' => $workouts->count(),
                'totalExercises' => $workouts->sum(fn ($w) => $w->exercises->count()),
            ],
        ]);
    }
}
