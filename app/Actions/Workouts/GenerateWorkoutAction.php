<?php

namespace App\Actions\Workouts;

use App\Models\Category;
use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateWorkoutAction
{
    public function execute(array $data): Workout
    {
        return DB::transaction(function () use ($data) {
            $categoryIds = $data['category_ids'];
            $client_id = $data['client_id'];
            $name = $data['name'] ?? $this->generateName($categoryIds);
            $description = $data['description'] ?? null;

            $exercises = $this->getExercisesForCategories($categoryIds);

            $workout = Workout::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $description,
                'client_id' => $client_id,
                'trainer_id' => auth()->id(),
                'is_active' => true,
            ]);

            $order = 0;
            foreach ($exercises as $exercise) {
                $workout->exercises()->attach($exercise->id, [
                    'sets' => $this->getDefaultSets($exercise->difficulty),
                    'reps' => $this->getDefaultReps($exercise->difficulty),
                    'rest_seconds' => $this->getDefaultRest($exercise->difficulty),
                    'order' => $order++,
                    'notes' => null,
                ]);
            }

            return $workout->load(['exercises.category']);
        });
    }

    private function generateName(array $categoryIds): string
    {
        $categories = Category::whereIn('id', $categoryIds)->pluck('name');

        if ($categories->count() === 1) {
            return 'Treino '.$categories->first().' - A';
        }

        return 'Treino '.$categories->take(2)->implode('/').' - ABC';
    }

    private function getExercisesForCategories(array $categoryIds): Collection
    {
        $exercisesByCategory = [];

        foreach ($categoryIds as $categoryId) {
            $categoryExercises = Exercise::where('category_id', $categoryId)
                ->where('is_active', true)
                ->inRandomOrder()
                ->limit($this->getExercisesPerCategory(count($categoryIds)))
                ->get();

            foreach ($categoryExercises as $exercise) {
                $exercisesByCategory[] = $exercise;
            }
        }

        return collect($exercisesByCategory);
    }

    private function getExercisesPerCategory(int $categoryCount): int
    {
        return match (true) {
            $categoryCount === 1 => 6,
            $categoryCount === 2 => 4,
            $categoryCount <= 4 => 3,
            default => 2,
        };
    }

    private function getDefaultSets(string $difficulty): int
    {
        return match ($difficulty) {
            'Beginner' => 3,
            'Intermediate' => 4,
            'Advanced' => 4,
            default => 3,
        };
    }

    private function getDefaultReps(string $difficulty): int
    {
        return match ($difficulty) {
            'Beginner' => 12,
            'Intermediate' => 10,
            'Advanced' => 8,
            default => 10,
        };
    }

    private function getDefaultRest(string $difficulty): int
    {
        return match ($difficulty) {
            'Beginner' => 90,
            'Intermediate' => 60,
            'Advanced' => 45,
            default => 60,
        };
    }
}
