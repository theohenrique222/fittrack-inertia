<?php

namespace App\Actions\Workouts;

use App\Models\Workout;
use Illuminate\Database\Eloquent\Collection;

class ListWorkoutsAction
{
    public function execute(array $filters = []): Collection
    {
        $query = Workout::with(['client.user', 'trainer', 'exercises.category']);

        if (isset($filters['search']) && ! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        if (isset($filters['client_id']) && ! empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        if (isset($filters['trainer_id']) && ! empty($filters['trainer_id'])) {
            $query->where('trainer_id', $filters['trainer_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
