<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Exercises\ListExercisesAction;
use App\Actions\Students\ListStudentsAction;
use App\Actions\Workouts\GenerateWorkoutAction;
use App\Actions\Workouts\ListWorkoutsAction;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workouts\GenerateWorkoutRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ExerciseResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\WorkoutResource;
use App\Models\Category;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class GenerateWorkoutController extends Controller
{
    public function __invoke(
        GenerateWorkoutRequest $request,
        GenerateWorkoutAction $action,
        ListStudentsAction $studentsAction,
        ListExercisesAction $exercisesAction
    ): Response {
        $validated = $request->validated();
        $user = Auth::user();

        if ($user?->role !== UserRole::ADMIN) {
            $studentId = $validated['client_id'];
            $studentOwnsWorkout = Client::where('id', $studentId)
                ->whereHas('user', fn ($q) => $q->where('trainer_id', $user?->id))
                ->exists();

            if (! $studentOwnsWorkout) {
                abort(403, 'Você não tem permissão para gerar treinos para este aluno.');
            }
        }

        $action->execute($validated);
        $workouts = (new ListWorkoutsAction)->execute(['trainer_id' => Auth::id()]);
        $students = $studentsAction->execute();
        $exercises = $exercisesAction->execute();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        $student = Client::with('user')->find($validated['client_id']);

        return Inertia::render('workouts/ListWorkouts', [
            'title' => 'Treinos',
            'studentId' => $student?->id,
            'student' => $student ? new StudentResource($student) : null,
            'workouts' => WorkoutResource::collection($workouts),
            'students' => StudentResource::collection($students),
            'exercises' => ExerciseResource::collection($exercises),
            'categories' => CategoryResource::collection($categories),
        ])->with('success', 'Treino gerado automaticamente com sucesso');
    }
}
