<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\Workout;
use App\Policies\WorkoutPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Workout::class => WorkoutPolicy::class,
    ];

    public function boot(): void
    {
        Gate::define('isAdmin', function ($user) {
            return $user?->role === UserRole::ADMIN;
        });

        Gate::define('isPersonal', function ($user) {
            return $user?->role === UserRole::PERSONAL;
        });

        Gate::define('isClient', function ($user) {
            return $user?->role === UserRole::CLIENT;
        });

        Gate::define('isSelf', function ($user) {
            return $user?->role === UserRole::SELF;
        });

        Gate::define('create-student', function ($user) {
            return $user?->role === UserRole::PERSONAL;
        });

        Gate::define('view-trainers', function ($user) {
            return $user?->role !== UserRole::CLIENT;
        });

        Gate::define('impersonate', function ($user) {
            return $user?->role === UserRole::ADMIN;
        });
    }
}
