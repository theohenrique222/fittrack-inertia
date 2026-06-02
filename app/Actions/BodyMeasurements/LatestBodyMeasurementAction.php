<?php

namespace App\Actions\BodyMeasurements;

use App\Models\Client;

class LatestBodyMeasurementAction
{
    public function execute(Client $client): ?array
    {
        $measurement = $client->bodyMeasurements()
            ->latest('recorded_at')
            ->first();

        if (! $measurement) {
            return null;
        }

        try {
            $calculator = new BodyMetricsCalculator($measurement, $client->user);
            $metrics = $calculator->calculate();

            return [
                'id' => $measurement->id,
                'recorded_at' => $measurement->recorded_at->format('d/m/Y'),
                'weight' => $measurement->weight,
                'height' => $measurement->height,
                'metrics' => $metrics,
            ];
        } catch (\RuntimeException $e) {
            return null;
        }
    }
}
