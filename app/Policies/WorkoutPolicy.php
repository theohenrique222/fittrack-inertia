<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Workout;

class WorkoutPolicy
{
    public function view(User $user, Workout $workout): bool
    {
        if ($user->role === UserRole::ADMIN) {
            return true;
        }

        if ($user->role === UserRole::PERSONAL) {
            return $workout->trainer_id === $user->id
                || $workout->client?->user?->trainer_id === $user->id;
        }

        if ($user->role === UserRole::CLIENT) {
            return $workout->client?->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->role === UserRole::PERSONAL || $user->role === UserRole::ADMIN;
    }

    public function update(User $user, Workout $workout): bool
    {
        if ($user->role === UserRole::ADMIN) {
            return true;
        }

        if ($user->role === UserRole::PERSONAL) {
            return $workout->trainer_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, Workout $workout): bool
    {
        return $this->update($user, $workout);
    }
}
