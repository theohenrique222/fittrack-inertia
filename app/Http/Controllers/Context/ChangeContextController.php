<?php

namespace App\Http\Controllers\Context;

use App\Enums\ContextsEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChangeContextController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $context = $request->input('context');

        if (! in_array($context, ['admin', 'personal'])) {
            return back()->withErrors([
                'context' => 'Contexto inválido',
            ]);
        }

        session(['context' => $context]);

        $route = match ($context) {
            ContextsEnum::ADMIN->value => 'dashboard',
            ContextsEnum::PERSONAL->value => 'students',
        };

        return redirect()->route($route)->with('success', 'Contexto alterado para '.$context);
    }
}
