<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Exercise;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Exercise>
 */
class ExerciseFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Supino Reto', 'Supino Inclinado', 'Crucifixo',
            'Remada Curvada', 'Puxada Frontal', 'Barra Fixa',
            'Desenvolvimento', 'Elevação Lateral', 'Elevação Frontal',
            'Rosca Direta', 'Rosca Martelo', 'Rosca Concentrada',
            'Tríceps Pulley', 'Tríceps Testa', 'Tríceps Francês',
            'Agachamento', 'Leg Press', 'Cadeira Extensora',
            'Mesa Flexora', 'Stiff', 'Elevação Pélvica',
            'Panturrilha em Pé', 'Panturrilha Sentado',
            'Abdominal Crunch', 'Prancha', 'Abdominal Oblíquo',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'category_id' => Category::factory(),
            'equipment' => fake()->randomElement(['Dumbbell', 'Barbell', 'Machine', 'Cable', 'Bodyweight']),
            'difficulty' => fake()->randomElement(['Beginner', 'Intermediate', 'Advanced']),
            'instructions' => fake()->paragraph(),
            'is_active' => true,
        ];
    }
}
