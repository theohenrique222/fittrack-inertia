<?php

namespace App\Actions\Students;

use App\Actions\BodyMeasurements\BodyMetricsCalculator;
use App\Http\Resources\StudentResource;
use App\Http\Resources\WorkoutResource;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ShowStudentAction
{
    public function __construct(
        private BodyMetricsCalculator $calculator,
    ) {}

    public function execute(Client $student): array
    {
        $student->loadMissing(['user', 'workouts.exercises.category']);

        $activeWorkout = $student->workouts
            ->where('is_active', true)
            ->first();

        $totalWorkouts = $student->workouts()->count();
        $activeWorkoutsCount = $student->workouts()->where('is_active', true)->count();

        $latestMeasurement = $student->bodyMeasurements()
            ->latest('recorded_at')
            ->first();

        $latestMeasurementData = null;
        if ($latestMeasurement && $student->user->gender && $student->user->birthdate) {
            try {
                $metrics = $this->calculator->calculate($latestMeasurement, $student->user);
                $latestMeasurementData = [
                    'id' => $latestMeasurement->id,
                    'recorded_at' => $latestMeasurement->recorded_at->format('d/m/Y'),
                    'weight' => (float) $latestMeasurement->weight,
                    'height' => (float) $latestMeasurement->height,
                    'metrics' => $metrics,
                ];
            } catch (\RuntimeException) {
            }
        }

        return [
            'student' => new StudentResource($student),
            'workout' => $activeWorkout ? new WorkoutResource($activeWorkout) : null,
            'workouts' => WorkoutResource::collection($student->workouts->sortByDesc('created_at')),
            'stats' => [
                'total_workouts' => $totalWorkouts,
                'active_workouts' => $activeWorkoutsCount,
                'created_at' => $student->created_at?->format('d/m/Y'),
                'trainer_name' => Auth::user()->name,
                'latest_measurement' => $latestMeasurementData,
            ],
        ];
    }
}
