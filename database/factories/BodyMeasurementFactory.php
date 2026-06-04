<?php

namespace Database\Factories;

use App\Enums\ActivityLevel;
use App\Enums\Goal;
use App\Models\BodyMeasurement;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class BodyMeasurementFactory extends Factory
{
    protected $model = BodyMeasurement::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'recorded_at' => fake()->dateTimeThisMonth(),
            'weight' => fake()->randomFloat(1, 40, 200),
            'height' => fake()->numberBetween(130, 220),
            'neck' => fake()->randomFloat(1, 25, 55),
            'waist' => fake()->randomFloat(1, 50, 160),
            'hip' => fake()->randomFloat(1, 50, 170),
            'chest' => fake()->randomFloat(1, 60, 170),
            'thigh' => fake()->randomFloat(1, 30, 100),
            'arm' => fake()->randomFloat(1, 20, 70),
            'forearm' => fake()->randomFloat(1, 15, 50),
            'calf' => fake()->randomFloat(1, 20, 60),
            'shoulders' => fake()->randomFloat(1, 60, 180),
            'activity_level' => fake()->randomElement(ActivityLevel::cases()),
            'goal' => fake()->randomElement(Goal::cases()),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
