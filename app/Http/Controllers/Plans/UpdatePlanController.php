<?php

namespace App\Http\Controllers\Plans;

use App\Actions\Plans\ListPlansAction;
use App\Actions\Plans\UpdatePlanAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Plans\UpdatePlanRequest;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use Inertia\Inertia;
use Inertia\Response;

class UpdatePlanController extends Controller
{
    public function __invoke(
        UpdatePlanRequest $request,
        Plan $plan,
        UpdatePlanAction $action,
    ): Response {
        $validated = $request->validated();

        $action->execute($plan, $validated);
        $plans = (new ListPlansAction)->execute();

        return Inertia::render('plans/ListPlans', [
            'title' => 'Planos',
            'plans' => PlanResource::collection($plans),
        ])->with('success', 'Plano atualizado com sucesso');
    }
}
