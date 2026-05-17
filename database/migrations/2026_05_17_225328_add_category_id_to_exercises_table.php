<?php

use App\Models\Category;
use App\Models\Exercise;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
        });

        $muscleGroupMap = [
            'Chest' => 'Peitoral',
            'Back' => 'Costas',
            'Shoulders' => 'Ombros',
            'Biceps' => 'Bíceps',
            'Triceps' => 'Tríceps',
            'Forearms' => 'Antebraços',
            'Abs' => 'Abdômen',
            'Quadriceps' => 'Quadríceps',
            'Hamstrings' => 'Posterior de Coxa',
            'Glutes' => 'Glúteos',
            'Calves' => 'Panturrilhas',
            'Full Body' => 'Corpo Inteiro',
        ];

        $categoryIds = [];

        foreach ($muscleGroupMap as $muscleGroup => $categoryName) {
            $category = Category::firstOrCreate(
                ['slug' => str($muscleGroup)->slug()],
                [
                    'name' => $categoryName,
                    'slug' => str($muscleGroup)->slug(),
                    'description' => "Exercícios para o grupo muscular {$categoryName}",
                    'is_active' => true,
                ]
            );
            $categoryIds[$muscleGroup] = $category->id;
        }

        Exercise::query()->whereNotNull('muscle_group')->each(function (Exercise $exercise) use ($categoryIds) {
            if (isset($categoryIds[$exercise->muscle_group])) {
                $exercise->updateQuietly(['category_id' => $categoryIds[$exercise->muscle_group]]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
