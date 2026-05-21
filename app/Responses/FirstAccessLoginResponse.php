<?php

namespace App\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class FirstAccessLoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        $user = $request->user();

        if ($user->mustResetPassword()) {
            return $request->wantsJson()
                ? new JsonResponse(['redirect' => route('password.must-reset')], 200)
                : redirect()->route('password.must-reset');
        }

        return $request->wantsJson()
            ? new JsonResponse('', 204)
            : redirect()->intended(config('fortify.home'));
    }
}
