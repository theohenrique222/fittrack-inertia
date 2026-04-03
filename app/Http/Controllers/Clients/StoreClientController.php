<?php

namespace App\Http\Controllers\Clients;

use App\Actions\Clients\StoreClientAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreClientController extends Controller
{
    public function __invoke(Request $request, StoreClientAction $action): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $action->execute($validated);

        return redirect()->route('clients')->with('success', 'Cliente criado com sucesso');
    }
}
