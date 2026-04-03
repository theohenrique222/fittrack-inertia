<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ListClientsController extends Controller
{
    public function __invoke(Request $request)
    {
        $clients = Client::with('user')->get()->map(function ($client) {
            return [
                'id' => $client->id,
                'name' => $client->user->name,
                'email' => $client->user->email,
                'nickname' => $client->user->nickname,
            ];
        });

        return Inertia::render('clients/ListClients', [
            'title' => 'Lista de Clientes',
            'clients' => $clients,
        ]);
    }
}
