<?php

namespace App\Http\Controllers\Clients;

use App\Actions\Clients\ListClientsAction;
use App\Actions\Clients\StoreClientAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class StoreClientController extends Controller
{
    public function __invoke(
        Request $request,
        StoreClientAction $action
    ): Response {
        Gate::authorize('create-client');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $action->execute($validated);
        $clients = (new ListClientsAction())->execute();

        return Inertia::render('clients/ListClients', [
            'title' => 'Lista de Clientes',
            'clients' => ClientResource::collection($clients),
        ])->with('success', 'Client created successfully');
    }
}
