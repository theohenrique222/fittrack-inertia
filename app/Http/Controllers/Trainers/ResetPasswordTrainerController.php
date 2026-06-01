<?php

namespace App\Http\Controllers\Trainers;

use App\Actions\Trainers\ResetPasswordTrainerAction;
use App\Http\Controllers\Controller;
use App\Models\Trainer;
use Illuminate\Http\RedirectResponse;

class ResetPasswordTrainerController extends Controller
{
    public function __invoke(
        Trainer $trainer,
        ResetPasswordTrainerAction $action
    ): RedirectResponse {
        $action->execute($trainer);

        return to_route('trainers')
            ->with('success', 'Senha redefinida com sucesso!');
    }
}
