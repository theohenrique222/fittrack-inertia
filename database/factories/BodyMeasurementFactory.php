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
            'weight' => fake()->randomFloat(1, 55, 120),
            'height' => fake()->numberBetween(150, 200),
            'neck' => fake()->randomFloat(1, 30, 45),
            'waist' => fake()->randomFloat(1, 60, 110),
            'hip' => fake()->randomFloat(1, 70, 120),
            'chest' => fake()->randomFloat(1, 80, 130),
            'thigh' => fake()->randomFloat(1, 40, 75),
            'arm' => fake()->randomFloat(1, 25, 50),
            'forearm' => fake()->randomFloat(1, 20, 35),
            'calf' => fake()->randomFloat(1, 25, 45),
            'shoulders' => fake()->randomFloat(1, 90, 140),
            'activity_level' => fake()->randomElement(ActivityLevel::cases()),
            'goal' => fake()->randomElement(Goal::cases()),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
