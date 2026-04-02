<?php

namespace App\Http\Controllers\Context;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChangeContextController extends Controller
{
    public function __invoke(Request $request)
    {
        $context = $request->input('context');

        // validação simples
        if (! in_array($context, ['admin', 'personal'])) {
            return back()->withErrors([
                'context' => 'Contexto inválido',
            ]);
        }

        // salva na sessão
        session(['context' => $context]);

        // redireciona (ajuste conforme seu sistema)
        return match ($context) {
            'admin' => abort(403, 'Painel do administrador'),
            'personal' => abort(403, 'Painel do personal'),
        };
    }
}
