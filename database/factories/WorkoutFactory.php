<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Workout>
 */
class WorkoutFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Treino A - Peito & Tríceps',
            'Treino B - Costas & Bíceps',
            'Treino C - Pernas',
            'Treino D - Ombros',
            'Treino E - Full Body',
            'Treino F - Core & Abdômen',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'client_id' => Client::factory(),
            'trainer_id' => User::factory(),
            'is_active' => true,
        ];
    }
}
