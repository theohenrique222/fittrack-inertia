<?php

namespace App\Actions\Exercises;

use App\Models\Exercise;
use Illuminate\Database\Eloquent\Collection;

class ListExercisesAction
{
    public function execute(array $filters = []): Collection
    {
        $query = Exercise::query();

        if (isset($filters['search']) && ! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        if (isset($filters['muscle_group']) && ! empty($filters['muscle_group'])) {
            $query->where('muscle_group', $filters['muscle_group']);
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
