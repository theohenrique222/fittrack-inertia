<?php

namespace Tests\Feature;

use App\Actions\Workouts\StoreExerciseCustomWeightAction;
use App\Models\Category;
use App\Models\Client;
use App\Models\Exercise;
use App\Models\ExerciseCustomWeight;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutExercise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutWeightTest extends TestCase
{
    use RefreshDatabase;

    public function test_exercise_workout_has_weight_column(): void
    {
        $this->assertTrue(
            \Schema::hasColumn('exercise_workout', 'weight'),
            'exercise_workout table should have a weight column'
        );
    }

    public function test_exercise_custom_weights_table_exists(): void
    {
        $this->assertTrue(
            \Schema::hasTable('exercise_custom_weights'),
            'exercise_custom_weights table should exist'
        );
    }

    public function test_weight_can_be_stored_on_pivot(): void
    {
        $user = User::factory()->create();
        $student = Client::factory()->create();
        $category = Category::factory()->create();

        $exercise = Exercise::create([
            'name' => 'Supino Reto',
            'slug' => 'supino-reto',
            'category_id' => $category->id,
            'difficulty' => 'Intermediate',
            'is_active' => true,
        ]);

        $workout = Workout::create([
            'name' => 'Treino Peso',
            'slug' => 'treino-peso',
            'client_id' => $student->id,
            'trainer_id' => $user->id,
            'is_active' => true,
        ]);

        $workout->exercises()->attach($exercise->id, [
            'sets' => 4,
            'reps' => 10,
            'rest_seconds' => 60,
            'weight' => 20.5,
            'order' => 0,
            'notes' => null,
        ]);

        $workout->load('exercises');

        $pivot = $workout->exercises->first()->pivot;

        $this->assertInstanceOf(WorkoutExercise::class, $pivot);
        $this->assertEquals(20.5, (float) $pivot->weight);
    }

    public function test_weight_is_nullable_on_pivot(): void
    {
        $user = User::factory()->create();
        $student = Client::factory()->create();
        $category = Category::factory()->create();

        $exercise = Exercise::create([
            'name' => 'Rosca Direta',
            'slug' => 'rosca-direta',
            'category_id' => $category->id,
            'difficulty' => 'Beginner',
            'is_active' => true,
        ]);

        $workout = Workout::create([
            'name' => 'Treino Sem Peso',
            'slug' => 'treino-sem-peso',
            'client_id' => $student->id,
            'trainer_id' => $user->id,
            'is_active' => true,
        ]);

        $workout->exercises()->attach($exercise->id, [
            'sets' => 3,
            'reps' => 12,
            'rest_seconds' => 60,
            'weight' => null,
            'order' => 0,
            'notes' => null,
        ]);

        $workout->load('exercises');

        $pivot = $workout->exercises->first()->pivot;

        $this->assertNull($pivot->weight);
    }

    public function test_custom_weight_can_be_stored(): void
    {
        $client = Client::factory()->create();
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $exercise = Exercise::create([
            'name' => 'Agachamento',
            'slug' => 'agachamento',
            'category_id' => $category->id,
            'difficulty' => 'Intermediate',
            'is_active' => true,
        ]);

        $workout = Workout::create([
            'name' => 'Treino Pernas',
            'slug' => 'treino-pernas',
            'client_id' => $client->id,
            'trainer_id' => $user->id,
            'is_active' => true,
        ]);

        $workout->exercises()->attach($exercise->id, [
            'sets' => 4,
            'reps' => 8,
            'rest_seconds' => 90,
            'weight' => 60.0,
            'order' => 0,
            'notes' => null,
        ]);

        $action = app(StoreExerciseCustomWeightAction::class);
        $customWeight = $action->execute($workout->id, $exercise->id, $client->id, 65.5);

        $this->assertInstanceOf(ExerciseCustomWeight::class, $customWeight);
        $this->assertEquals(65.5, (float) $customWeight->weight);
        $this->assertEquals($workout->id, $customWeight->workout_id);
        $this->assertEquals($exercise->id, $customWeight->exercise_id);
        $this->assertEquals($client->id, $customWeight->client_id);
    }

    public function test_custom_weight_takes_priority_over_trainer_weight(): void
    {
        $client = Client::factory()->create();
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $exercise = Exercise::create([
            'name' => 'Leg Press',
            'slug' => 'leg-press',
            'category_id' => $category->id,
            'difficulty' => 'Intermediate',
            'is_active' => true,
        ]);

        $workout = Workout::create([
            'name' => 'Treino Inferior',
            'slug' => 'treino-inferior',
            'client_id' => $client->id,
            'trainer_id' => $user->id,
            'is_active' => true,
        ]);

        $workout->exercises()->attach($exercise->id, [
            'sets' => 4,
            'reps' => 10,
            'rest_seconds' => 60,
            'weight' => 80.0,
            'order' => 0,
            'notes' => null,
        ]);

        $action = app(StoreExerciseCustomWeightAction::class);
        $action->execute($workout->id, $exercise->id, $client->id, 90.0);

        $customWeight = ExerciseCustomWeight::where('workout_id', $workout->id)
            ->where('exercise_id', $exercise->id)
            ->where('client_id', $client->id)
            ->first();

        $this->assertNotNull($customWeight);
        $this->assertEquals(90.0, (float) $customWeight->weight);
    }

    public function test_custom_weight_can_be_cleared(): void
    {
        $client = Client::factory()->create();
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $exercise = Exercise::create([
            'name' => 'Cadeira Extensora',
            'slug' => 'cadeira-extensora',
            'category_id' => $category->id,
            'difficulty' => 'Beginner',
            'is_active' => true,
        ]);

        $workout = Workout::create([
            'name' => 'Treino Quadriceps',
            'slug' => 'treino-quadriceps',
            'client_id' => $client->id,
            'trainer_id' => $user->id,
            'is_active' => true,
        ]);

        $workout->exercises()->attach($exercise->id, [
            'sets' => 3,
            'reps' => 12,
            'rest_seconds' => 60,
            'weight' => 30.0,
            'order' => 0,
            'notes' => null,
        ]);

        $action = app(StoreExerciseCustomWeightAction::class);

        $action->execute($workout->id, $exercise->id, $client->id, 35.0);

        $action->execute($workout->id, $exercise->id, $client->id, null);

        $customWeight = ExerciseCustomWeight::where('workout_id', $workout->id)
            ->where('exercise_id', $exercise->id)
            ->where('client_id', $client->id)
            ->first();

        $this->assertNotNull($customWeight);
        $this->assertNull($customWeight->weight);
    }

    public function test_exercise_workout_pivot_includes_weight(): void
    {
        $workout = Workout::factory()->create();
        $exercise = Exercise::factory()->create();

        $workout->exercises()->attach($exercise->id, [
            'sets' => 3,
            'reps' => 10,
            'rest_seconds' => 60,
            'weight' => 15.5,
            'order' => 0,
            'notes' => null,
        ]);

        $pivot = WorkoutExercise::where('workout_id', $workout->id)
            ->where('exercise_id', $exercise->id)
            ->first();

        $this->assertNotNull($pivot);
        $this->assertEquals(15.5, (float) $pivot->weight);
    }
}
