<?php

use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Clients\ListClientsController;
use App\Http\Controllers\Context\ChangeContextController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Clients\StoreClientController;
use Illuminate\Routing\Router;


/** @var Router $router */
$router = app(Router::class);

$router
    ->get(uri: '/', action: LoginController::class)
    ->name('home');

$router
    ->post(uri: 'change-context', action: ChangeContextController::class)
    ->name('change-context');

$router
    ->get(uri: '/clients', action: ListClientsController::class)
    ->name('clients');

$router
    ->post(uri: '/clients', action: StoreClientController::class)
    ->name('clients.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});



require __DIR__.'/settings.php';
