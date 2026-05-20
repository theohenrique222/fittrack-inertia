<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Workouts\DestroyWorkoutAction;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Workout;
use Illuminate\Support\Facades\Auth;

class DestroyWorkoutController extends Controller
{
    public function __invoke(
        Workout $workout,
        DestroyWorkoutAction $action,
    ) {
        $user = Auth::user();

        if ($user?->role !== UserRole::ADMIN && $workout->trainer_id !== $user?->id) {
            abort(403, 'Você não tem permissão para deletar este treino.');
        }

        $studentId = $workout->client_id;
        $action->execute($workout);

        return redirect()
            ->route('students.show', $studentId)
            ->with('success', 'Treino deletado com sucesso');
    }
}
