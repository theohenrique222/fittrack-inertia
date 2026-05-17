<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Peitoral', 'slug' => 'chest', 'description' => 'Exercícios para o peitoral'],
            ['name' => 'Costas', 'slug' => 'back', 'description' => 'Exercícios para as costas'],
            ['name' => 'Ombros', 'slug' => 'shoulders', 'description' => 'Exercícios para os ombros'],
            ['name' => 'Bíceps', 'slug' => 'biceps', 'description' => 'Exercícios para bíceps'],
            ['name' => 'Tríceps', 'slug' => 'triceps', 'description' => 'Exercícios para tríceps'],
            ['name' => 'Antebraços', 'slug' => 'forearms', 'description' => 'Exercícios para antebraços'],
            ['name' => 'Abdômen', 'slug' => 'abs', 'description' => 'Exercícios para o abdômen'],
            ['name' => 'Quadríceps', 'slug' => 'quadriceps', 'description' => 'Exercícios para quadríceps'],
            ['name' => 'Posterior de Coxa', 'slug' => 'hamstrings', 'description' => 'Exercícios para posterior de coxa'],
            ['name' => 'Glúteos', 'slug' => 'glutes', 'description' => 'Exercícios para glúteos'],
            ['name' => 'Panturrilhas', 'slug' => 'calves', 'description' => 'Exercícios para panturrilhas'],
            ['name' => 'Corpo Inteiro', 'slug' => 'full-body', 'description' => 'Exercícios para o corpo inteiro'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
