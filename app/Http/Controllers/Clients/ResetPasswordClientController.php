<?php

namespace App\Http\Controllers\Clients;

use App\Actions\Clients\ResetPasswordClientAction;
use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;

class ResetPasswordClientController extends Controller
{
    public function __invoke(
        Client $client,
        ResetPasswordClientAction $action
    ): RedirectResponse {
        $action->execute($client);

        return to_route('clients')
            ->with('message', 'Senha redefinida com sucesso!');
    }
}
