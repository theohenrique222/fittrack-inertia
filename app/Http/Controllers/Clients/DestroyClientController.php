<?php

namespace App\Http\Controllers\Clients;

use App\Actions\Clients\DestroyClientAction;
use App\Actions\Clients\ListClientsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Inertia\Inertia;
use Inertia\Response;

class DestroyClientController extends Controller
{
    public function __invoke(
        Client $client,
        DestroyClientAction $action
    ): Response {
        $action->execute($client);
        $clients = (new ListClientsAction())->execute();

        return Inertia::render('clients/ListClients', [
            'title' => 'Lista de Clientes',
            'clients' => ClientResource::collection($clients),
        ]);
    }
}

