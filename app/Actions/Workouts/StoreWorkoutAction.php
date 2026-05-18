<?php

namespace App\Actions\Workouts;

use App\Models\Workout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreWorkoutAction
{
    public function execute(array $data): Workout
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = Str::slug($data['name']);
            $data['trainer_id'] = auth()->id();

            $exercises = $data['exercises'];
            unset($data['exercises']);

            $workout = Workout::create($data);

            foreach ($exercises as $exercise) {
                $workout->exercises()->attach($exercise['exercise_id'], [
                    'sets' => $exercise['sets'],
                    'reps' => $exercise['reps'],
                    'rest_seconds' => $exercise['rest_seconds'],
                    'order' => $exercise['order'],
                    'notes' => $exercise['notes'] ?? null,
                ]);
            }

            return $workout->load(['exercises.category']);
        });
    }
}
