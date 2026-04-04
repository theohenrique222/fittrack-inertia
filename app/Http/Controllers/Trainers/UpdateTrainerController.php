<?php

namespace App\Http\Controllers\Trainers;

use App\Actions\Trainers\ListTrainersAction;
use App\Actions\Trainers\UpdateTrainerAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrainerResource;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UpdateTrainerController extends Controller
{
    public function __invoke(
        Request $request,
        Trainer $trainer,
        UpdateTrainerAction $action
    ): Response {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'specialty' => ['nullable', 'string', 'max:255'],
        ]);

        $updatedTrainer = $action->execute($trainer, $validated);
        $trainers = (new ListTrainersAction())->execute();

        return Inertia::render('trainers/ListTrainers', [
            'title' => 'Lista de Treinadores',
            'trainers' => TrainerResource::collection($trainers),
        ]);
    }
}

