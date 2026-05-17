<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Peitoral', 'Costas', 'Ombros', 'Bíceps', 'Tríceps',
            'Antebraços', 'Abdômen', 'Quadríceps', 'Posterior de Coxa',
            'Glúteos', 'Panturrilhas', 'Corpo Inteiro',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
