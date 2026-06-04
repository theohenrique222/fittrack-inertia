<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Workouts\ListWorkoutsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\WorkoutResource;
use App\Models\Client;
use App\Models\WorkoutSession;
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

        $workouts = $action->execute($filters, withMetrics: true);
        $workouts->load('completions');

        $sessionHistory = WorkoutSession::where('client_id', $client->id)
            ->where('status', 'completed')
            ->with('workout')
            ->orderBy('completed_at', 'desc')
            ->limit(50)
            ->get();

        return Inertia::render('workouts/StudentListWorkouts', [
            'workouts' => WorkoutResource::collection($workouts),
            'sessionHistory' => $sessionHistory->map(fn ($s) => [
                'id' => $s->id,
                'workout_id' => $s->workout_id,
                'workout_name' => $s->workout->name,
                'completed_at' => $s->completed_at->format('d/m/Y'),
                'completed_at_full' => $s->completed_at->toISOString(),
                'duration_minutes' => $s->duration_minutes,
            ]),
            'stats' => [
                'total' => $workouts->count(),
                'totalExercises' => $workouts->sum(fn ($w) => $w->exercises->count()),
                'totalCompleted' => $workouts->sum(fn ($w) => (int) ($w->total_sessions ?? 0)),
            ],
        ]);
    }
}
