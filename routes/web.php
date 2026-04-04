<?php

use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Clients\ListClientsController;
use App\Http\Controllers\Clients\StoreClientController;
use App\Http\Controllers\Context\ChangeContextController;
use App\Http\Controllers\Trainers\ListTrainersController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/** @var Router $router */
$router = app(Router::class);

$router
    ->get(uri: '/', action: fn () => Inertia::render('Welcome'))
    ->name('welcome');

$router

    ->get(uri: '/login', action: LoginController::class)
    ->name('login');

$router
    ->post(uri: 'change-context', action: ChangeContextController::class)
    ->name('change-context');

$router
    ->get(uri: '/clients', action: ListClientsController::class)
    ->name('clients');

$router
    ->get(uri: '/trainers', action: ListTrainersController::class)
    ->name('trainers');

$router
    ->post(uri: '/clients', action: StoreClientController::class)
    ->name('clients.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
