<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Exercises\ListExercisesAction;
use App\Actions\Students\ListStudentsAction;
use App\Actions\Workouts\DestroyWorkoutAction;
use App\Actions\Workouts\ListWorkoutsAction;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ExerciseResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\WorkoutResource;
use App\Models\Category;
use App\Models\Client;
use App\Models\Workout;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DestroyWorkoutController extends Controller
{
    public function __invoke(
        Workout $workout,
        DestroyWorkoutAction $action,
        ListStudentsAction $studentsAction,
        ListExercisesAction $exercisesAction
    ): Response {
        $user = Auth::user();

        if ($user?->role !== UserRole::ADMIN && $workout->trainer_id !== $user?->id) {
            abort(403, 'Você não tem permissão para deletar este treino.');
        }

        $clientId = $workout->client_id;
        $action->execute($workout);
        $workouts = (new ListWorkoutsAction)->execute(['trainer_id' => Auth::id()]);
        $students = $studentsAction->execute();
        $exercises = $exercisesAction->execute();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $client = Client::with('user')->find($clientId);

        return Inertia::render('workouts/ListWorkouts', [
            'title' => 'Treinos',
            'clientId' => $client?->id,
            'student' => $client ? new StudentResource($client) : null,
            'workouts' => WorkoutResource::collection($workouts),
            'students' => StudentResource::collection($students),
            'exercises' => ExerciseResource::collection($exercises),
            'categories' => CategoryResource::collection($categories),
        ])->with('success', 'Treino deletado com sucesso');
    }
}
