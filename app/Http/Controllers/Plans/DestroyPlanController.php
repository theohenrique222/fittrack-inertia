<?php

namespace App\Http\Controllers\Plans;

use App\Actions\Plans\DestroyPlanAction;
use App\Actions\Plans\ListPlansAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use Inertia\Inertia;
use Inertia\Response;

class DestroyPlanController extends Controller
{
    public function __invoke(
        Plan $plan,
        DestroyPlanAction $action,
    ): Response {
        $action->execute($plan);
        $plans = (new ListPlansAction)->execute();

        return Inertia::render('plans/ListPlans', [
            'title' => 'Planos',
            'plans' => PlanResource::collection($plans),
        ])->with('success', 'Plano deletado com sucesso');
    }
}
