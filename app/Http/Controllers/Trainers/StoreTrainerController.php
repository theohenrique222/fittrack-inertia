<?php

namespace App\Http\Controllers\Trainers;

use App\Actions\Trainers\ListTrainersAction;
use App\Actions\Trainers\StoreTrainerAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\TrainerResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StoreTrainerController extends Controller
{
    public function __invoke(
        Request $request,
        StoreTrainerAction $action
    ): Response {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'specialty' => ['nullable', 'string', 'max:255'],
        ]);

        $action->execute($validated);
        $trainers = (new ListTrainersAction)->execute();

        return Inertia::render('trainers/ListTrainers', [
            'title' => 'Lista de Treinadores',
            'trainers' => TrainerResource::collection($trainers),
        ]);
    }
}
