<?php

namespace App\Providers;

use App\Enums\ContextsEnum;
use Carbon\CarbonImmutable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        $this->configureDefaults();

        Inertia::share('context', function () {
            $contexts = collect(ContextsEnum::cases());

            return [
                'currentContext' => session('context', ContextsEnum::ADMIN->value),

                'availableContexts' => $contexts->mapWithKeys(fn ($context) => [
                    $context->value => $context->label(),
                ]),
            ];
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            $this->app->isLocal() === false,
        );

        $this->preventTestingDatabaseCollision();

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Prevent testing database from using the development database.
     */
    protected function preventTestingDatabaseCollision(): void
    {
        if (app()->environment('testing')) {
            $testingDatabase = config('database.connections.mysql.database');
            $developmentDatabase = env('DB_DATABASE');

            if ($testingDatabase === $developmentDatabase) {
                abort(500, 'Testing database cannot be the same as development database.');
            }
        }
    }
}
