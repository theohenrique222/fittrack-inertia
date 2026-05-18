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

        Gate::define('create-client', function ($user) {
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
