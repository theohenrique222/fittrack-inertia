<?php

namespace App\Actions\Exercises;

use App\Models\Category;
use App\Models\Exercise;
use Illuminate\Support\Str;

class UpdateExerciseAction
{
    public function execute(Exercise $exercise, array $data): Exercise
    {
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (isset($data['category_id']) && (! isset($data['muscle_group']) || $data['category_id'] !== $exercise->category_id)) {
            $category = Category::find($data['category_id']);
            $data['muscle_group'] = $category?->name ?? '';
        }

        $exercise->update($data);

        return $exercise->fresh();
    }
}
