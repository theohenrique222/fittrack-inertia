<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ListClientsController extends Controller
{
    public function __invoke(Request $request)
    {
        $clients = [
            ['name' => 'Theodoro', 'age' => 28],
            ['name' => 'Maria', 'age' => 25],
        ];

        return Inertia::render('clients/ListClients', [
            'title' => 'Lista de Clientes',
            'clients' => $clients,
        ]);
    }
}
