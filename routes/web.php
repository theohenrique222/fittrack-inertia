<?php

use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Categories\ListCategoriesController;
use App\Http\Controllers\Clients\DestroyClientController;
use App\Http\Controllers\Clients\ListClientsController;
use App\Http\Controllers\Clients\ResetPasswordClientController;
use App\Http\Controllers\Clients\StoreClientController;
use App\Http\Controllers\Clients\UpdateClientController;
use App\Http\Controllers\Context\ChangeContextController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Exercises\DestroyExerciseController;
use App\Http\Controllers\Exercises\ListExercisesController;
use App\Http\Controllers\Exercises\StoreExerciseController;
use App\Http\Controllers\Exercises\UpdateExerciseController;
use App\Http\Controllers\Trainers\DestroyTrainerController;
use App\Http\Controllers\Trainers\ListTrainersController;
use App\Http\Controllers\Trainers\StoreTrainerController;
use App\Http\Controllers\Trainers\UpdateTrainerController;
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
    ->name('clients')
    ->middleware('auth');

$router
    ->post(uri: '/clients', action: StoreClientController::class)
    ->name('clients.store');

$router
    ->put(uri: '/clients/{client}', action: UpdateClientController::class)
    ->name('clients.update');

$router
    ->delete(uri: '/clients/{client}', action: DestroyClientController::class)
    ->name('clients.destroy');

$router
    ->post(uri: '/clients/{client}/reset-password', action: ResetPasswordClientController::class)
    ->name('clients.reset-password');

$router
    ->get(uri: '/trainers', action: ListTrainersController::class)
    ->name('trainers')
    ->middleware(['auth', 'can:view-trainers']);

$router
    ->post(uri: '/trainers', action: StoreTrainerController::class)
    ->name('trainers.store');

$router
    ->put(uri: '/trainers/{trainer}', action: UpdateTrainerController::class)
    ->name('trainers.update');

$router
    ->delete(uri: '/trainers/{trainer}', action: DestroyTrainerController::class)
    ->name('trainers.destroy');

$router
    ->get(uri: '/exercises', action: ListExercisesController::class)
    ->name('exercises')
    ->middleware('auth');

$router
    ->post(uri: '/exercises', action: StoreExerciseController::class)
    ->name('exercises.store');

$router
    ->put(uri: '/exercises/{exercise}', action: UpdateExerciseController::class)
    ->name('exercises.update');

$router
    ->delete(uri: '/exercises/{exercise}', action: DestroyExerciseController::class)
    ->name('exercises.destroy');

$router
    ->get(uri: '/categories', action: ListCategoriesController::class)
    ->name('categories')
    ->middleware('auth');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
});

require __DIR__.'/settings.php';
