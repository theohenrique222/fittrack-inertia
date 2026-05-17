<?php

namespace App\Actions\Exercises;

use App\Models\Category;
use App\Models\Exercise;
use Illuminate\Support\Str;

class StoreExerciseAction
{
    public function execute(array $data): Exercise
    {
        $data['slug'] = Str::slug($data['name']);

        if (! isset($data['muscle_group']) && isset($data['category_id'])) {
            $category = Category::find($data['category_id']);
            $data['muscle_group'] = $category?->name ?? '';
        }

        return Exercise::create($data);
    }
}
