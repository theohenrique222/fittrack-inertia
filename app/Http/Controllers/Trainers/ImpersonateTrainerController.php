<?php

namespace App\Http\Controllers\Trainers;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ImpersonateTrainerController extends Controller
{
    public function __invoke(Trainer $trainer): RedirectResponse
    {
        Gate::authorize('isAdmin');

        $originalUserId = Auth::id();

        session([
            'impersonating' => true,
            'impersonator_id' => $originalUserId,
            'impersonator_name' => Auth::user()->name,
        ]);

        Auth::login($trainer->user);

        return redirect()->route('dashboard')->with('info', 'Você está personificando '.$trainer->user->name);
    }
}
