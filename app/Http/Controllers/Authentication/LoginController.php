<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class LoginController extends Controller
{
    public function __invoke(): RedirectResponse|InertiaResponse
    {
        if (Auth::check()) {
            return to_route('dashboard');
        }
        return Inertia::render( component: 'auth/Login');
    }
}
