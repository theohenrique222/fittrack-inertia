<?php

namespace App\Http\Controllers\BodyMeasurements;

use App\Actions\BodyMeasurements\LatestBodyMeasurementAction;
use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;

class LatestBodyMeasurementController extends Controller
{
    public function __invoke(
        Client $student,
        LatestBodyMeasurementAction $action,
    ): JsonResponse {
        $result = $action->execute($student);

        if (! $result) {
            return response()->json(['data' => null]);
        }

        return response()->json(['data' => $result]);
    }
}
