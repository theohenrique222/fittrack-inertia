<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Exercise;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_categories_table_exists(): void
    {
        $this->assertTrue(
            \Schema::hasTable('categories'),
            'Categories table should exist in the database'
        );
    }

    public function test_category_can_be_created(): void
    {
        $category = Category::factory()->create([
            'name' => 'Peitoral',
            'slug' => 'peitoral',
        ]);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Peitoral', $category->name);
        $this->assertEquals('peitoral', $category->slug);
    }

    public function test_category_has_many_exercises(): void
    {
        $category = Category::factory()->create();

        $exercise1 = Exercise::create([
            'name' => 'Bench Press',
            'slug' => 'bench-press',
            'category_id' => $category->id,
            'muscle_group' => 'Chest',
            'difficulty' => 'Intermediate',
            'is_active' => true,
        ]);

        $exercise2 = Exercise::create([
            'name' => 'Incline Bench Press',
            'slug' => 'incline-bench-press',
            'category_id' => $category->id,
            'muscle_group' => 'Chest',
            'difficulty' => 'Intermediate',
            'is_active' => true,
        ]);

        $this->assertCount(2, $category->exercises);
        $this->assertTrue($category->exercises->contains($exercise1));
        $this->assertTrue($category->exercises->contains($exercise2));
    }

    public function test_category_is_active_casts_to_boolean(): void
    {
        $category = Category::factory()->create(['is_active' => 1]);

        $this->assertIsBool($category->is_active);
        $this->assertTrue($category->is_active);
    }

    public function test_category_has_correct_fillable_fields(): void
    {
        $category = new Category;

        $expectedFillable = [
            'name',
            'slug',
            'description',
            'icon',
            'is_active',
        ];

        $this->assertEquals($expectedFillable, $category->getFillable());
    }
}
