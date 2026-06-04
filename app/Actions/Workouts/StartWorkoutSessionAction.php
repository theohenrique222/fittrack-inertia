<?php

namespace App\Actions\Workouts;

use App\Models\Client;
use App\Models\Workout;
use App\Models\WorkoutSession;

readonly class StartWorkoutSessionAction
{
    public function execute(Workout $workout, Client $client): WorkoutSession
    {
        return WorkoutSession::create([
            'workout_id' => $workout->id,
            'client_id' => $client->id,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);
    }
}
