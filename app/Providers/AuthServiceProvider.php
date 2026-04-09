<?php

namespace App\Providers;

use App\Enums\UserRole;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('isAdmin', function ($user) {
            return $user?->role === UserRole::ADMIN->value;
        });

        Gate::define('isPersonal', function ($user) {
            return $user?->role === UserRole::PERSONAL->value;
        });

        Gate::define('isClient', function ($user) {
            return $user?->role === UserRole::CLIENT->value;
        });

        Gate::define('isSelf', function ($user) {
            return $user?->role === UserRole::SELF->value;
        });

        Gate::define('create-client', function ($user) {
            return $user?->role === UserRole::TRAINER->value;
        });
    }
}

