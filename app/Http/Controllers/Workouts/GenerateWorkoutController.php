<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Workouts\GenerateWorkoutAction;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Workouts\GenerateWorkoutRequest;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class GenerateWorkoutController extends Controller
{
    public function __invoke(
        GenerateWorkoutRequest $request,
        GenerateWorkoutAction $action,
    ) {
        $validated = $request->validated();
        logger()->info('GenerateWorkoutController called', $validated);
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

        return redirect()
            ->route('students.show', $validated['client_id'])
            ->with('success', 'Treino gerado automaticamente com sucesso');
    }
}
