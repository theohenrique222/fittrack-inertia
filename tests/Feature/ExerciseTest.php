<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Exercise;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ExerciseTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_exercises_table_exists(): void
    {
        $this->assertTrue(
            \Schema::hasTable('exercises'),
            'Exercises table should exist in the database'
        );
    }

    public function test_exercise_model_can_be_created_with_category(): void
    {
        $category = Category::factory()->create();

        $exercise = Exercise::create([
            'name' => 'Bench Press',
            'slug' => 'bench-press',
            'description' => 'A compound exercise that works the chest',
            'category_id' => $category->id,
            'muscle_group' => 'Chest',
            'equipment' => 'Barbell',
            'difficulty' => 'Intermediate',
            'instructions' => 'Lie on a flat bench and press the barbell up',
            'video_url' => 'https://example.com/video',
            'image' => 'https://example.com/image.jpg',
            'is_active' => true,
        ]);

        $this->assertInstanceOf(Exercise::class, $exercise);
        $this->assertEquals('Bench Press', $exercise->name);
        $this->assertEquals('bench-press', $exercise->slug);
        $this->assertEquals($category->id, $exercise->category_id);
        $this->assertTrue($exercise->category->is($category));
    }

    public function test_exercise_can_be_updated(): void
    {
        $category = Category::factory()->create();
        $newCategory = Category::factory()->create(['slug' => 'new-category']);

        $exercise = Exercise::create([
            'name' => 'Squats',
            'slug' => 'squats',
            'description' => 'A compound lower body exercise',
            'category_id' => $category->id,
            'muscle_group' => 'Quadriceps',
            'equipment' => 'Barbell',
            'difficulty' => 'Intermediate',
            'is_active' => true,
        ]);

        $exercise->update([
            'difficulty' => 'Advanced',
            'is_active' => false,
            'category_id' => $newCategory->id,
        ]);

        $this->assertEquals('Advanced', $exercise->difficulty);
        $this->assertFalse($exercise->is_active);
        $this->assertEquals($newCategory->id, $exercise->category_id);
    }

    public function test_exercise_can_be_deleted(): void
    {
        $category = Category::factory()->create();

        $exercise = Exercise::create([
            'name' => 'Deadlift',
            'slug' => 'deadlift',
            'description' => 'A compound full body exercise',
            'category_id' => $category->id,
            'muscle_group' => 'Full Body',
            'equipment' => 'Barbell',
            'difficulty' => 'Advanced',
            'is_active' => true,
        ]);

        $exerciseId = $exercise->id;

        $exercise->delete();

        $this->assertNull(Exercise::find($exerciseId));
    }

    public function test_exercise_has_correct_fillable_fields(): void
    {
        $exercise = new Exercise;

        $expectedFillable = [
            'name',
            'slug',
            'description',
            'muscle_group',
            'category_id',
            'equipment',
            'difficulty',
            'instructions',
            'video_url',
            'image',
            'is_active',
        ];

        $this->assertEquals($expectedFillable, $exercise->getFillable());
    }

    public function test_exercise_is_active_casts_to_boolean(): void
    {
        $category = Category::factory()->create();

        $exercise = Exercise::create([
            'name' => 'Push Ups',
            'slug' => 'push-ups',
            'category_id' => $category->id,
            'muscle_group' => 'Chest',
            'difficulty' => 'Beginner',
            'is_active' => 1,
        ]);

        $this->assertIsBool($exercise->is_active);
        $this->assertTrue($exercise->is_active);
    }

    public function test_exercise_belongs_to_category(): void
    {
        $category = Category::factory()->create();

        $exercise = Exercise::create([
            'name' => 'Pull Ups',
            'slug' => 'pull-ups',
            'category_id' => $category->id,
            'muscle_group' => 'Back',
            'difficulty' => 'Intermediate',
            'is_active' => true,
        ]);

        $this->assertInstanceOf(Category::class, $exercise->category);
        $this->assertEquals($category->id, $exercise->category->id);
    }
}
