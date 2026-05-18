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
    protected static int $index = 0;

    public function definition(): array
    {
        $names = [
            'Peitoral', 'Costas', 'Ombros', 'Bíceps', 'Tríceps',
            'Antebraços', 'Abdômen', 'Quadríceps', 'Posterior de Coxa',
            'Glúteos', 'Panturrilhas', 'Corpo Inteiro',
        ];

        $name = $names[static::$index % count($names)];
        static::$index++;

        return [
            'name' => $name.' '.fake()->unique()->word,
            'slug' => Str::slug($name).'-'.fake()->unique()->word,
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
