<?php

namespace App\Http\Controllers\Plans;

use App\Actions\Plans\ListPlansAction;
use App\Actions\Plans\StorePlanAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Plans\StorePlanRequest;
use App\Http\Resources\PlanResource;
use Inertia\Inertia;
use Inertia\Response;

class StorePlanController extends Controller
{
    public function __invoke(
        StorePlanRequest $request,
        StorePlanAction $action,
    ): Response {
        $validated = $request->validated();

        $action->execute($validated);
        $plans = (new ListPlansAction)->execute();

        return Inertia::render('plans/ListPlans', [
            'title' => 'Planos',
            'plans' => PlanResource::collection($plans),
        ])->with('success', 'Plano criado com sucesso');
    }
}
