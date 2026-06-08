<?php

namespace App\Actions\Plans;

use App\Models\Plan;

class DestroyPlanAction
{
    public function execute(Plan $plan): void
    {
        $plan->delete();
    }
}
