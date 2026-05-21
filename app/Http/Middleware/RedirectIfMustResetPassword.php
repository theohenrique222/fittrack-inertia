<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfMustResetPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $freshUser = $user->fresh();
            if ($freshUser && $freshUser->must_reset_password === true) {
                $allowedRoutes = ['password.must-reset', 'logout'];

                if (! in_array($request->route()->getName(), $allowedRoutes)) {
                    return redirect()->route('password.must-reset');
                }
            }
        }

        return $next($request);
    }
}
