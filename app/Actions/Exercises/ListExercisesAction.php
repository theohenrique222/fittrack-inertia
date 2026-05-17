<?php

namespace App\Actions\Exercises;

use App\Models\Exercise;
use Illuminate\Database\Eloquent\Collection;

class ListExercisesAction
{
    public function execute(array $filters = []): Collection
    {
        $query = Exercise::with('category');

        if (isset($filters['search']) && ! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        if (isset($filters['category_id']) && ! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['difficulty']) && ! empty($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        if (isset($filters['equipment']) && ! empty($filters['equipment'])) {
            $query->where('equipment', $filters['equipment']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
