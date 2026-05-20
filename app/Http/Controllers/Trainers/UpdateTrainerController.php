<?php

namespace App\Http\Controllers\Trainers;

use App\Actions\Trainers\UpdateTrainerAction;
use App\Http\Controllers\Controller;
use App\Models\Trainer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UpdateTrainerController extends Controller
{
    public function __invoke(
        Request $request,
        Trainer $trainer,
        UpdateTrainerAction $action
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'specialty' => ['nullable', 'string', 'max:255'],
        ]);

        $action->execute($trainer, $validated);

        return redirect()
            ->route('trainers')
            ->with('success', 'Treinador atualizado com sucesso.');
    }
}
