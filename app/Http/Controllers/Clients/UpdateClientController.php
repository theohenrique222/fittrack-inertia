<?php

namespace App\Http\Controllers\Clients;

use App\Actions\Clients\ListClientsAction;
use App\Actions\Clients\UpdateClientAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UpdateClientController extends Controller
{
    public function __invoke(
        Request $request,
        Client $client,
        UpdateClientAction $action
    ): Response {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'nickname' => ['nullable', 'string', 'max:255'],
        ]);

        $updatedClient = $action->execute($client, $validated);
        $clients = (new ListClientsAction())->execute();

        return Inertia::render('clients/ListClients', [
            'title' => 'Lista de Clientes',
            'clients' => ClientResource::collection($clients),
        ]);
    }
}

