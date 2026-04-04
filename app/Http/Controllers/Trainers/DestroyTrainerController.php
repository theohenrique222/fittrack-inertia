<?php

namespace App\Http\Controllers\Trainers;

use App\Actions\Trainers\DestroyTrainerAction;
use App\Actions\Trainers\ListTrainersAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrainerResource;
use App\Models\Trainer;
use Inertia\Inertia;
use Inertia\Response;

class DestroyTrainerController extends Controller
{
    public function __invoke(
        Trainer $trainer,
        DestroyTrainerAction $action
    ): Response {
        $action->execute($trainer);
        $trainers = (new ListTrainersAction())->execute();

        return Inertia::render('trainers/ListTrainers', [
            'title' => 'Lista de Treinadores',
            'trainers' => TrainerResource::collection($trainers),
        ]);
    }
}

