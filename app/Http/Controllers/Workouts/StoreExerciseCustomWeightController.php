<?php

namespace App\Http\Controllers\Workouts;

use App\Actions\Workouts\StoreExerciseCustomWeightAction;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StoreExerciseCustomWeightController extends Controller
{
    public function __invoke(
        Workout $workout,
        Exercise $exercise,
        Request $request,
        StoreExerciseCustomWeightAction $action,
    ): RedirectResponse {
        $this->authorize('view', $workout);

        $validated = $request->validate([
            'weight' => ['nullable', 'numeric', 'min:0'],
        ]);

        $client = Client::where('user_id', $request->user()->id)->first();

        if (! $client) {
            throw ValidationException::withMessages([
                'weight' => 'Apenas alunos podem personalizar peso.',
            ]);
        }

        $action->execute(
            $workout->id,
            $exercise->id,
            $client->id,
            $validated['weight'] !== null ? (float) $validated['weight'] : null,
        );

        return redirect()->back()->with('success', 'Peso atualizado com sucesso');
    }
}
