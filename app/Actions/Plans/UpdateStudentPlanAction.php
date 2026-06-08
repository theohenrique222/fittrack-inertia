<?php

namespace App\Actions\Plans;

use App\Models\Client;
use App\Models\Plan;

class UpdateStudentPlanAction
{
    public function execute(Client $student, ?int $planId): Client
    {
        $plan = $planId ? Plan::findOrFail($planId) : null;

        $student->changePlan($plan);

        return $student->fresh(['plan']);
    }
}
