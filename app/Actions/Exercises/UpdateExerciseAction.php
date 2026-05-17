<?php

namespace App\Actions\Exercises;

use App\Models\Exercise;
use Illuminate\Support\Str;

class UpdateExerciseAction
{
    public function execute(Exercise $exercise, array $data): Exercise
    {
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $exercise->update($data);

        return $exercise->fresh();
    }
}
