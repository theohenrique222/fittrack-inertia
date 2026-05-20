<?php

namespace App\Http\Controllers\Trainers;

use App\Actions\Trainers\DestroyTrainerAction;
use App\Http\Controllers\Controller;
use App\Models\Trainer;
use Illuminate\Http\RedirectResponse;

class DestroyTrainerController extends Controller
{
    public function __invoke(
        Trainer $trainer,
        DestroyTrainerAction $action
    ): RedirectResponse {
        $action->execute($trainer);

        return redirect()
            ->route('trainers')
            ->with('success', 'Treinador removido com sucesso.');
    }
}
