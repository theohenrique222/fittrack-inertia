<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompleteProfileController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'gender' => ['required', 'string', 'in:male,female'],
            'birthdate' => ['required', 'date', 'before:today'],
        ]);

        $request->user()->fill($validated)->save();

        return redirect()->back()->with('success', 'Perfil atualizado com sucesso!');
    }
}
