<?php

namespace App\Actions\Exercises;

use App\Models\Exercise;

class DestroyExerciseAction
{
    public function execute(Exercise $exercise): void
    {
        $exercise->delete();
    }
}
