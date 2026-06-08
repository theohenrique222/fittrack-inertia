<?php

namespace App\Actions\Workouts;

use App\Models\Workout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateWorkoutAction
{
    public function execute(Workout $workout, array $data): Workout
    {
        return DB::transaction(function () use ($workout, $data) {
            if (isset($data['name'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            $exercises = $data['exercises'];
            unset($data['exercises']);

            $workout->update($data);

            $workout->exercises()->detach();

            foreach ($exercises as $exercise) {
                $workout->exercises()->attach($exercise['exercise_id'], [
                    'sets' => $exercise['sets'],
                    'reps' => $exercise['reps'],
                    'rest_seconds' => $exercise['rest_seconds'],
                    'weight' => $exercise['weight'] ?? null,
                    'order' => $exercise['order'],
                    'notes' => $exercise['notes'] ?? null,
                ]);
            }

            return $workout->fresh(['exercises.category']);
        });
    }
}
