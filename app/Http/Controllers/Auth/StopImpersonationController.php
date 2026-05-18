<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class StopImpersonationController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $impersonatorId = session('impersonator_id');

        session()->forget(['impersonating', 'impersonator_id', 'impersonator_name']);

        if ($impersonatorId) {
            $user = User::find($impersonatorId);

            if ($user) {
                Auth::login($user);

                return redirect()->route('dashboard')->with('info', 'Você voltou à sua conta original.');
            }
        }

        return redirect()->route('login');
    }
}
