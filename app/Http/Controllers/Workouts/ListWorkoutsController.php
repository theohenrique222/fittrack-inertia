<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Exercises\ListExercisesAction;
use App\Actions\Workouts\ListWorkoutsAction;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ExerciseResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\WorkoutResource;
use App\Models\Category;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ListWorkoutsController extends Controller
{
    public function __invoke(
        ListWorkoutsAction $action,
        ListExercisesAction $exercisesAction
    ): Response {
        $studentId = request()->query('student_id');

        if (! $studentId) {
            return Inertia::render('errors/Error', [
                'title' => 'Erro',
                'message' => 'Selecione um aluno para visualizar os treinos.',
                'status' => 403,
            ]);
        }

        $student = Client::with('user')->find($studentId);

        if (! $student) {
            return Inertia::render('errors/Error', [
                'title' => 'Erro',
                'message' => 'Aluno não encontrado.',
                'status' => 404,
            ]);
        }

        $user = Auth::user();

        if ($user?->role !== UserRole::ADMIN && $student->user?->trainer_id !== $user?->id) {
            return Inertia::render('errors/Error', [
                'title' => 'Erro',
                'message' => 'Você não tem permissão para visualizar os treinos deste aluno.',
                'status' => 403,
            ]);
        }

        $filters = request()->only(['search', 'is_active']);
        $filters['trainer_id'] = Auth::id();
        $filters['client_id'] = $studentId;

        $workouts = $action->execute($filters);
        $exercises = $exercisesAction->execute();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('workouts/ListWorkouts', [
            'title' => 'Treinos',
            'studentId' => (int) $studentId,
            'student' => new StudentResource($student),
            'workouts' => WorkoutResource::collection($workouts),
            'exercises' => ExerciseResource::collection($exercises),
            'categories' => CategoryResource::collection($categories),
        ]);
    }
}
