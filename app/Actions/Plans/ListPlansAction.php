<?php

namespace App\Actions\Plans;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Collection;

class ListPlansAction
{
    public function execute(array $filters = []): Collection
    {
        $query = Plan::withCount('clients');

        if (isset($filters['search']) && ! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
