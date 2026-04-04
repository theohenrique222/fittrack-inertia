<?php

namespace App\Http\Controllers\Trainers;

use App\Actions\Trainers\ListTrainersAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrainerResource;
use Inertia\Inertia;
use Inertia\Response;

class ListTrainersController extends Controller
{
    public function __invoke(ListTrainersAction $action): Response
    {
        $trainers = $action->execute();

        return Inertia::render('trainers/ListTrainers', [
            'title' => 'Lista de Treinadores',
            'trainers' => TrainerResource::collection($trainers),
        ]);
    }
}
