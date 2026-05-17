<?php

namespace App\Actions\Exercises;

use App\Models\Exercise;
use Illuminate\Support\Str;

class StoreExerciseAction
{
    public function execute(array $data): Exercise
    {
        $data['slug'] = Str::slug($data['name']);

        return Exercise::create($data);
    }
}
