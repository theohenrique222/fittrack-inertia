<?php

namespace App\Actions\BodyMeasurements;

use App\Models\BodyMeasurement;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class StoreBodyMeasurementAction
{
    public function execute(Client $client, array $data): array
    {
        return DB::transaction(function () use ($client, $data) {
            $measurement = BodyMeasurement::create([
                'client_id' => $client->id,
                'recorded_at' => $data['recorded_at'] ?? now(),
                'weight' => $data['weight'],
                'height' => $data['height'],
                'neck' => $data['neck'] ?? null,
                'waist' => $data['waist'] ?? null,
                'hip' => $data['hip'] ?? null,
                'chest' => $data['chest'] ?? null,
                'thigh' => $data['thigh'] ?? null,
                'arm' => $data['arm'] ?? null,
                'forearm' => $data['forearm'] ?? null,
                'calf' => $data['calf'] ?? null,
                'shoulders' => $data['shoulders'] ?? null,
                'activity_level' => $data['activity_level'],
                'goal' => $data['goal'] ?? 'maintain',
                'notes' => $data['notes'] ?? null,
            ]);

            $calculator = new BodyMetricsCalculator($measurement, $client->user);
            $metrics = $calculator->calculate();

            return [
                'measurement' => $measurement,
                'metrics' => $metrics,
            ];
        });
    }
}
