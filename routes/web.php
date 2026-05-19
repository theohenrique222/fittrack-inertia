<?php

use App\Http\Controllers\Auth\StopImpersonationController;
use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\Categories\ListCategoriesController;
use App\Http\Controllers\Context\ChangeContextController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Exercises\DestroyExerciseController;
use App\Http\Controllers\Exercises\ListExercisesController;
use App\Http\Controllers\Exercises\StoreExerciseController;
use App\Http\Controllers\Exercises\UpdateExerciseController;
use App\Http\Controllers\Students\DestroyStudentController;
use App\Http\Controllers\Students\ListStudentsController;
use App\Http\Controllers\Students\ResetPasswordStudentController;
use App\Http\Controllers\Students\ShowStudentController;
use App\Http\Controllers\Students\StoreStudentController;
use App\Http\Controllers\Students\UpdateStudentController;
use App\Http\Controllers\Trainers\DestroyTrainerController;
use App\Http\Controllers\Trainers\ImpersonateTrainerController;
use App\Http\Controllers\Trainers\ListTrainersController;
use App\Http\Controllers\Trainers\StoreTrainerController;
use App\Http\Controllers\Trainers\UpdateTrainerController;
use App\Http\Controllers\Workouts\DestroyWorkoutController;
use App\Http\Controllers\Workouts\GenerateWorkoutController;
use App\Http\Controllers\Workouts\ListWorkoutsController;
use App\Http\Controllers\Workouts\StoreWorkoutController;
use App\Http\Controllers\Workouts\UpdateWorkoutController;
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
    ->get(uri: '/students', action: ListStudentsController::class)
    ->name('students')
    ->middleware('auth');

$router
    ->get(uri: '/students/{student}', action: ShowStudentController::class)
    ->name('students.show')
    ->middleware('auth');

$router
    ->post(uri: '/students', action: StoreStudentController::class)
    ->name('students.store');

$router
    ->put(uri: '/students/{student}', action: UpdateStudentController::class)
    ->name('students.update');

$router
    ->delete(uri: '/students/{student}', action: DestroyStudentController::class)
    ->name('students.destroy');

$router
    ->post(uri: '/students/{student}/reset-password', action: ResetPasswordStudentController::class)
    ->name('students.reset-password');

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
    ->post(uri: '/trainers/{trainer}/impersonate', action: ImpersonateTrainerController::class)
    ->name('trainers.impersonate')
    ->middleware(['auth', 'can:isAdmin']);

$router
    ->post(uri: '/stop-impersonate', action: StopImpersonationController::class)
    ->name('stop-impersonate')
    ->middleware('auth');

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

$router
    ->get(uri: '/students/{student}/workouts', action: ListWorkoutsController::class)
    ->name('students.workouts')
    ->middleware('auth');

$router
    ->post(uri: '/workouts', action: StoreWorkoutController::class)
    ->name('workouts.store');

$router
    ->post(uri: '/workouts/generate', action: GenerateWorkoutController::class)
    ->name('workouts.generate');

$router
    ->put(uri: '/workouts/{workout}', action: UpdateWorkoutController::class)
    ->name('workouts.update');

$router
    ->delete(uri: '/workouts/{workout}', action: DestroyWorkoutController::class)
    ->name('workouts.destroy');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
});

require __DIR__.'/settings.php';
