<?php

namespace App\Http\Controllers\Trainers;

use App\Actions\Trainers\StoreTrainerAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreTrainerController extends Controller
{
    public function __invoke(Request $request, StoreTrainerAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'specialty' => ['nullable', 'string', 'max:255'],
        ]);

        $action->execute($validated);

        return redirect()->route('trainers')->with('success', 'Personal Trainer criado com sucesso');
    }
}
