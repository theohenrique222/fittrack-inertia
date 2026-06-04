<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Client;
use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_workouts_table_exists(): void
    {
        $this->assertTrue(
            \Schema::hasTable('workouts'),
            'Workouts table should exist in the database'
        );
    }

    public function test_exercise_workout_table_exists(): void
    {
        $this->assertTrue(
            \Schema::hasTable('exercise_workout'),
            'Exercise workout pivot table should exist in the database'
        );
    }

    public function test_workout_model_can_be_created(): void
    {
        $user = User::factory()->create();
        $student = Client::factory()->create();

        $workout = Workout::create([
            'name' => 'Treino A - Peito & Tríceps',
            'slug' => 'treino-a-peito-triceps',
            'description' => 'Treino focado em peito e tríceps',
            'client_id' => $student->id,
            'trainer_id' => $user->id,
            'is_active' => true,
        ]);

        $this->assertInstanceOf(Workout::class, $workout);
        $this->assertEquals('Treino A - Peito & Tríceps', $workout->name);
        $this->assertEquals($student->id, $workout->client_id);
        $this->assertEquals($user->id, $workout->trainer_id);
    }

    public function test_workout_can_be_updated(): void
    {
        $user = User::factory()->create();
        $student = Client::factory()->create();

        $workout = Workout::create([
            'name' => 'Treino A',
            'slug' => 'treino-a',
            'client_id' => $student->id,
            'trainer_id' => $user->id,
            'is_active' => true,
        ]);

        $workout->update([
            'name' => 'Treino A - Peito',
            'is_active' => false,
        ]);

        $this->assertEquals('Treino A - Peito', $workout->name);
        $this->assertFalse($workout->is_active);
    }

    public function test_workout_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $student = Client::factory()->create();

        $workout = Workout::create([
            'name' => 'Treino B',
            'slug' => 'treino-b',
            'client_id' => $student->id,
            'trainer_id' => $user->id,
            'is_active' => true,
        ]);

        $workoutId = $workout->id;

        $workout->delete();

        $this->assertNull(Workout::find($workoutId));
    }

    public function test_workout_has_correct_fillable_fields(): void
    {
        $workout = new Workout;

        $expectedFillable = [
            'name',
            'slug',
            'description',
            'client_id',
            'trainer_id',
            'is_active',
        ];

        $this->assertEquals($expectedFillable, $workout->getFillable());
    }

    public function test_workout_is_active_casts_to_boolean(): void
    {
        $user = User::factory()->create();
        $student = Client::factory()->create();

        $workout = Workout::create([
            'name' => 'Treino C',
            'slug' => 'treino-c',
            'client_id' => $student->id,
            'trainer_id' => $user->id,
            'is_active' => 1,
        ]);

        $this->assertIsBool($workout->is_active);
        $this->assertTrue($workout->is_active);
    }

    public function test_workout_belongs_to_student(): void
    {
        $student = Client::factory()->create();
        $user = User::factory()->create();

        $workout = Workout::create([
            'name' => 'Treino D',
            'slug' => 'treino-d',
            'client_id' => $student->id,
            'trainer_id' => $user->id,
            'is_active' => true,
        ]);

        $this->assertInstanceOf(Client::class, $workout->client);
        $this->assertEquals($student->id, $workout->client->id);
    }

    public function test_workout_belongs_to_trainer(): void
    {
        $student = Client::factory()->create();
        $user = User::factory()->create();

        $workout = Workout::create([
            'name' => 'Treino E',
            'slug' => 'treino-e',
            'client_id' => $student->id,
            'trainer_id' => $user->id,
            'is_active' => true,
        ]);

        $this->assertInstanceOf(User::class, $workout->trainer);
        $this->assertEquals($user->id, $workout->trainer->id);
    }

    public function test_workout_can_have_exercises(): void
    {
        $user = User::factory()->create();
        $student = Client::factory()->create();
        $category = Category::factory()->create();

        $exercise1 = Exercise::create([
            'name' => 'Supino Reto',
            'slug' => 'supino-reto',
            'category_id' => $category->id,
            'difficulty' => 'Intermediate',
            'is_active' => true,
        ]);

        $exercise2 = Exercise::create([
            'name' => 'Crucifixo',
            'slug' => 'crucifixo',
            'category_id' => $category->id,
            'difficulty' => 'Beginner',
            'is_active' => true,
        ]);

        $workout = Workout::create([
            'name' => 'Treino F',
            'slug' => 'treino-f',
            'client_id' => $student->id,
            'trainer_id' => $user->id,
            'is_active' => true,
        ]);

        $workout->exercises()->attach($exercise1->id, [
            'sets' => 4,
            'reps' => 10,
            'rest_seconds' => 90,
            'order' => 0,
            'notes' => 'Carga progressiva',
        ]);

        $workout->exercises()->attach($exercise2->id, [
            'sets' => 3,
            'reps' => 12,
            'rest_seconds' => 60,
            'order' => 1,
        ]);

        $this->assertEquals(2, $workout->exercises->count());
        $this->assertEquals('Supino Reto', $workout->exercises->first()->name);
    }

    public function test_exercise_workout_pivot_has_correct_data(): void
    {
        $user = User::factory()->create();
        $student = Client::factory()->create();
        $category = Category::factory()->create();

        $exercise = Exercise::create([
            'name' => 'Supino Inclinado',
            'slug' => 'supino-inclinado',
            'category_id' => $category->id,
            'difficulty' => 'Intermediate',
            'is_active' => true,
        ]);

        $workout = Workout::create([
            'name' => 'Treino G',
            'slug' => 'treino-g',
            'client_id' => $student->id,
            'trainer_id' => $user->id,
            'is_active' => true,
        ]);

        $workout->exercises()->attach($exercise->id, [
            'sets' => 4,
            'reps' => 8,
            'rest_seconds' => 120,
            'order' => 0,
            'notes' => 'Foco na contração',
        ]);

        $workout->load('exercises');

        $pivot = $workout->exercises->first()->pivot;

        $this->assertEquals(4, $pivot->sets);
        $this->assertEquals(8, $pivot->reps);
        $this->assertEquals(120, $pivot->rest_seconds);
        $this->assertEquals(0, $pivot->order);
        $this->assertEquals('Foco na contração', $pivot->notes);
    }

    public function test_student_has_workouts_relationship(): void
    {
        $student = Client::factory()->create();
        $user = User::factory()->create();

        Workout::create([
            'name' => 'Treino H',
            'slug' => 'treino-h',
            'client_id' => $student->id,
            'trainer_id' => $user->id,
            'is_active' => true,
        ]);

        Workout::create([
            'name' => 'Treino I',
            'slug' => 'treino-i',
            'client_id' => $student->id,
            'trainer_id' => $user->id,
            'is_active' => true,
        ]);

        $this->assertEquals(2, $student->workouts->count());
    }

    public function test_trainer_has_workouts_relationship(): void
    {
        $student = Client::factory()->create();
        $user = User::factory()->create();

        Workout::create([
            'name' => 'Treino J',
            'slug' => 'treino-j',
            'client_id' => $student->id,
            'trainer_id' => $user->id,
            'is_active' => true,
        ]);

        $this->assertEquals(1, $user->workouts->count());
    }

    public function test_workout_has_sessions_relationship(): void
    {
        $workout = Workout::factory()->create();

        $session = $workout->sessions()->create([
            'client_id' => $workout->client_id,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        $this->assertInstanceOf(WorkoutSession::class, $session);
        $this->assertEquals($workout->id, $session->workout_id);
        $this->assertEquals(1, $workout->sessions->count());
    }

    public function test_workout_remains_active_after_session_completion(): void
    {
        $workout = Workout::factory()->create();

        $session = $workout->sessions()->create([
            'client_id' => $workout->client_id,
            'started_at' => now()->subHour(),
            'completed_at' => now(),
            'duration_minutes' => 60,
            'status' => 'completed',
        ]);

        $workout->refresh();

        $this->assertTrue($workout->is_active);
        $this->assertNull($workout->completed_at);
        $this->assertEquals('completed', $session->status);
    }
}
