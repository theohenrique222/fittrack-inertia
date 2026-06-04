<?php

namespace App\Actions\BodyMeasurements;

use App\Models\BodyMeasurement;

class DestroyBodyMeasurementAction
{
    public function execute(BodyMeasurement $measurement): void
    {
        $measurement->delete();
    }
}
