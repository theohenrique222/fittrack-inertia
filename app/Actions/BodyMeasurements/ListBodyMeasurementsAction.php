<?php

namespace App\Actions\BodyMeasurements;

use App\Models\Client;
use Illuminate\Pagination\LengthAwarePaginator;

class ListBodyMeasurementsAction
{
    public function execute(Client $client, ?int $perPage = 15): LengthAwarePaginator
    {
        $measurements = $client->bodyMeasurements()
            ->orderBy('recorded_at', 'desc')
            ->paginate($perPage);

        $measurements->getCollection()->transform(function ($measurement) use ($client) {
            try {
                $calculator = new BodyMetricsCalculator($measurement, $client->user);
                $metrics = $calculator->calculate();

                return (object) [
                    'id' => $measurement->id,
                    'recorded_at' => $measurement->recorded_at->format('d/m/Y'),
                    'weight' => $measurement->weight,
                    'height' => $measurement->height,
                    'neck' => $measurement->neck,
                    'waist' => $measurement->waist,
                    'hip' => $measurement->hip,
                    'chest' => $measurement->chest,
                    'thigh' => $measurement->thigh,
                    'arm' => $measurement->arm,
                    'forearm' => $measurement->forearm,
                    'calf' => $measurement->calf,
                    'shoulders' => $measurement->shoulders,
                    'activity_level' => $measurement->activity_level?->label(),
                    'goal' => $measurement->goal?->label(),
                    'notes' => $measurement->notes,
                    'metrics' => $metrics,
                ];
            } catch (\RuntimeException) {
                return (object) [
                    'id' => $measurement->id,
                    'recorded_at' => $measurement->recorded_at->format('d/m/Y'),
                    'weight' => $measurement->weight,
                    'height' => $measurement->height,
                    'neck' => $measurement->neck,
                    'waist' => $measurement->waist,
                    'hip' => $measurement->hip,
                    'chest' => $measurement->chest,
                    'thigh' => $measurement->thigh,
                    'arm' => $measurement->arm,
                    'forearm' => $measurement->forearm,
                    'calf' => $measurement->calf,
                    'shoulders' => $measurement->shoulders,
                    'activity_level' => $measurement->activity_level?->label(),
                    'goal' => $measurement->goal?->label(),
                    'notes' => $measurement->notes,
                    'metrics' => null,
                ];
            }
        });

        return $measurements;
    }
}
