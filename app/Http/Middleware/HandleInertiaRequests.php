<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
                'client_id' => $request->user()?->client?->id,
                'can' => [
                    'create_student' => $request->user()?->can('create-student'),
                    'view_students' => $request->user()?->role !== UserRole::CLIENT,
                    'view_trainers' => $request->user()?->role !== UserRole::CLIENT && $request->user()?->role !== UserRole::PERSONAL,
                    'impersonate' => $request->user()?->can('impersonate'),
                ],
                'impersonating' => session('impersonating', false),
                'impersonator_name' => session('impersonator_name'),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info' => $request->session()->get('info'),
            ],
        ];
    }
}
