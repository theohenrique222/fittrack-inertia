<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UpdateFirstPasswordController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'new_password' => ['required', 'string', Password::default(), 'confirmed'],
        ]);

        $request->user()->forceFill([
            'password' => Hash::make($validated['new_password']),
            'must_reset_password' => false,
        ])->save();

        return redirect()->route('dashboard')
            ->with('success', 'Senha alterada com sucesso! Agora você tem acesso completo ao sistema.');
    }
}
