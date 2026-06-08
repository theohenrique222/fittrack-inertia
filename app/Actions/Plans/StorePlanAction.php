<?php

namespace App\Actions\Plans;

use App\Models\Plan;

class StorePlanAction
{
    public function execute(array $data): Plan
    {
        return Plan::create($data);
    }
}
