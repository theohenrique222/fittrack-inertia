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
                'neck' => filled($data['neck'] ?? null) ? $data['neck'] : null,
                'waist' => filled($data['waist'] ?? null) ? $data['waist'] : null,
                'hip' => filled($data['hip'] ?? null) ? $data['hip'] : null,
                'chest' => filled($data['chest'] ?? null) ? $data['chest'] : null,
                'thigh' => filled($data['thigh'] ?? null) ? $data['thigh'] : null,
                'arm' => filled($data['arm'] ?? null) ? $data['arm'] : null,
                'forearm' => filled($data['forearm'] ?? null) ? $data['forearm'] : null,
                'calf' => filled($data['calf'] ?? null) ? $data['calf'] : null,
                'shoulders' => filled($data['shoulders'] ?? null) ? $data['shoulders'] : null,
                'activity_level' => $data['activity_level'],
                'goal' => $data['goal'] ?? 'maintain',
                'notes' => $data['notes'] ?? null,
            ]);

            try {
                $calculator = new BodyMetricsCalculator($measurement, $client->user);
                $metrics = $calculator->calculate();
            } catch (\RuntimeException) {
                $metrics = [
                    'bmi' => ['value' => 0, 'classification' => 'Não calculado', 'color' => '#6b7280'],
                    'body_fat' => ['value' => 0, 'classification' => 'Não calculado', 'color' => '#6b7280'],
                    'lean_mass' => 0,
                    'fat_mass' => 0,
                    'bmr' => 0,
                    'tdee' => 0,
                    'daily_water' => 0,
                    'macros' => [
                        'calories' => 0,
                        'protein' => ['grams' => 0, 'percentage' => 0],
                        'carbs' => ['grams' => 0, 'percentage' => 0],
                        'fat' => ['grams' => 0, 'percentage' => 0],
                    ],
                ];
            }

            return [
                'measurement' => $measurement,
                'metrics' => $metrics,
            ];
        });
    }
}
