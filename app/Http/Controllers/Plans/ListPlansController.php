<?php

namespace App\Http\Controllers\Plans;

use App\Actions\Plans\ListPlansAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use Inertia\Inertia;
use Inertia\Response;

class ListPlansController extends Controller
{
    public function __invoke(ListPlansAction $action): Response
    {
        $filters = request()->only(['search', 'is_active']);
        $plans = $action->execute($filters);

        return Inertia::render('plans/ListPlans', [
            'title' => 'Planos',
            'plans' => PlanResource::collection($plans),
        ]);
    }
}
