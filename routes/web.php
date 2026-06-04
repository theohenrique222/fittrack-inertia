<?php

use App\Http\Controllers\Auth\MustResetPasswordController;
use App\Http\Controllers\Auth\StopImpersonationController;
use App\Http\Controllers\Auth\UpdateFirstPasswordController;
use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\BodyMeasurements\DestroyBodyMeasurementController;
use App\Http\Controllers\BodyMeasurements\LatestBodyMeasurementController;
use App\Http\Controllers\BodyMeasurements\ListBodyMeasurementsController;
use App\Http\Controllers\BodyMeasurements\StoreBodyMeasurementController;
use App\Http\Controllers\BodyMeasurements\UpdateBodyMeasurementController;
use App\Http\Controllers\Categories\ListCategoriesController;
use App\Http\Controllers\Context\ChangeContextController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Exercises\DestroyExerciseController;
use App\Http\Controllers\Exercises\ListExercisesController;
use App\Http\Controllers\Exercises\StoreExerciseController;
use App\Http\Controllers\Exercises\UpdateExerciseController;
use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\Settings\CompleteProfileController;
use App\Http\Controllers\Students\DestroyStudentController;
use App\Http\Controllers\Students\ListAllStudentsController;
use App\Http\Controllers\Students\ListMeasurementsOverviewController;
use App\Http\Controllers\Students\ListStudentsController;
use App\Http\Controllers\Students\ResetPasswordStudentController;
use App\Http\Controllers\Students\ShowStudentController;
use App\Http\Controllers\Students\StoreStudentController;
use App\Http\Controllers\Students\UpdateStudentController;
use App\Http\Controllers\Trainers\DestroyTrainerController;
use App\Http\Controllers\Trainers\ImpersonateTrainerController;
use App\Http\Controllers\Trainers\ListTrainersController;
use App\Http\Controllers\Trainers\ResetPasswordTrainerController;
use App\Http\Controllers\Trainers\StoreTrainerController;
use App\Http\Controllers\Trainers\UpdateTrainerController;
use App\Http\Controllers\Workouts\CompleteWorkoutController;
use App\Http\Controllers\Workouts\DestroyWorkoutController;
use App\Http\Controllers\Workouts\GenerateWorkoutController;
use App\Http\Controllers\Workouts\ListStudentWorkoutsController;
use App\Http\Controllers\Workouts\ShowWorkoutDetailController;
use App\Http\Controllers\Workouts\StoreWorkoutController;
use App\Http\Controllers\Workouts\ToggleExerciseCompletionController;
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
    ->get(uri: '/password/must-reset', action: MustResetPasswordController::class)
    ->name('password.must-reset')
    ->middleware('auth');

$router
    ->post(uri: '/password/update-first', action: UpdateFirstPasswordController::class)
    ->name('password.update-first')
    ->middleware('auth');

$router
    ->post(uri: 'change-context', action: ChangeContextController::class)
    ->name('change-context');

$router
    ->get(uri: '/students', action: ListStudentsController::class)
    ->name('students')
    ->middleware('auth');

$router
    ->get(uri: '/admin/students', action: ListAllStudentsController::class)
    ->name('admin.students')
    ->middleware(['auth', 'can:isAdmin']);

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
    ->get(uri: '/students/{student}/measurements', action: ListBodyMeasurementsController::class)
    ->name('students.measurements')
    ->middleware('auth');

$router
    ->post(uri: '/students/{student}/measurements', action: StoreBodyMeasurementController::class)
    ->name('students.measurements.store')
    ->middleware('auth');

$router
    ->get(uri: '/students/{student}/measurements/latest', action: LatestBodyMeasurementController::class)
    ->name('students.measurements.latest')
    ->middleware('auth');

$router
    ->delete(uri: '/students/{student}/measurements/{measurement}', action: DestroyBodyMeasurementController::class)
    ->name('students.measurements.destroy')
    ->middleware('auth');

$router
    ->put(uri: '/students/{student}/measurements/{measurement}', action: UpdateBodyMeasurementController::class)
    ->name('students.measurements.update')
    ->middleware('auth');

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
    ->post(uri: '/trainers/{trainer}/reset-password', action: ResetPasswordTrainerController::class)
    ->name('trainers.reset-password')
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
    ->redirect(uri: '/students/{student}/workouts', destination: '/students/{student}')
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
    ->get(uri: '/workouts/{workout}', action: ShowWorkoutDetailController::class)
    ->name('workouts.show')
    ->middleware('auth');

$router
    ->post(uri: '/workouts/{workout}/exercises/{exercise}/toggle-completion', action: ToggleExerciseCompletionController::class)
    ->name('workouts.exercises.toggle-completion')
    ->middleware('auth');

$router
    ->post(uri: '/workouts/{workout}/complete', action: CompleteWorkoutController::class)
    ->name('workouts.complete')
    ->middleware('auth');

$router
    ->delete(uri: '/workouts/{workout}', action: DestroyWorkoutController::class)
    ->name('workouts.destroy');

Route::middleware(['auth'])->group(function () {
    Route::post('profile/complete', CompleteProfileController::class)->name('profile.complete');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('measurements', ListMeasurementsOverviewController::class)->name('measurements');
    Route::get('reports', ReportsController::class)->name('reports');
    Route::get('me/workouts', ListStudentWorkoutsController::class)->name('me.workouts');
});

require __DIR__.'/settings.php';
