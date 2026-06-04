<?php

namespace Tests\Feature;

use App\Actions\Workouts\CompleteWorkoutAction;
use App\Actions\Workouts\StartWorkoutSessionAction;
use App\Actions\Workouts\ToggleExerciseCompletionAction;
use App\Models\Client;
use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_workout_sessions_table_exists(): void
    {
        $this->assertTrue(
            \Schema::hasTable('workout_sessions'),
            'Workout sessions table should exist in the database'
        );
    }

    public function test_can_create_workout_session(): void
    {
        $workout = Workout::factory()->create();

        $session = WorkoutSession::create([
            'workout_id' => $workout->id,
            'client_id' => $workout->client_id,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        $this->assertInstanceOf(WorkoutSession::class, $session);
        $this->assertEquals('in_progress', $session->status);
        $this->assertNull($session->completed_at);
    }

    public function test_workout_session_belongs_to_workout(): void
    {
        $workout = Workout::factory()->create();
        $session = WorkoutSession::create([
            'workout_id' => $workout->id,
            'client_id' => $workout->client_id,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        $this->assertInstanceOf(Workout::class, $session->workout);
        $this->assertEquals($workout->id, $session->workout->id);
    }

    public function test_workout_session_belongs_to_client(): void
    {
        $client = Client::factory()->create();
        $workout = Workout::factory()->create(['client_id' => $client->id]);
        $session = WorkoutSession::create([
            'workout_id' => $workout->id,
            'client_id' => $client->id,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        $this->assertInstanceOf(Client::class, $session->client);
        $this->assertEquals($client->id, $session->client->id);
    }

    public function test_multiple_sessions_can_exist_for_same_workout(): void
    {
        $workout = Workout::factory()->create();

        WorkoutSession::create([
            'workout_id' => $workout->id,
            'client_id' => $workout->client_id,
            'started_at' => now()->subDays(2),
            'completed_at' => now()->subDays(2)->addHour(),
            'duration_minutes' => 60,
            'status' => 'completed',
        ]);

        WorkoutSession::create([
            'workout_id' => $workout->id,
            'client_id' => $workout->client_id,
            'started_at' => now()->subDay(),
            'completed_at' => now()->subDay()->addMinutes(45),
            'duration_minutes' => 45,
            'status' => 'completed',
        ]);

        WorkoutSession::create([
            'workout_id' => $workout->id,
            'client_id' => $workout->client_id,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        $this->assertEquals(3, $workout->sessions()->count());
        $this->assertEquals(2, $workout->sessions()->where('status', 'completed')->count());
    }

    public function test_start_workout_session_action(): void
    {
        $workout = Workout::factory()->create();
        $client = Client::find($workout->client_id);

        $action = new StartWorkoutSessionAction;
        $session = $action->execute($workout, $client);

        $this->assertInstanceOf(WorkoutSession::class, $session);
        $this->assertEquals($workout->id, $session->workout_id);
        $this->assertEquals($client->id, $session->client_id);
        $this->assertEquals('in_progress', $session->status);
        $this->assertNotNull($session->started_at);
    }

    public function test_complete_workout_session_action(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $client = Client::factory()->create(['user_id' => $user->id]);
        $workout = Workout::factory()->create(['client_id' => $client->id]);
        $exercise = Exercise::factory()->create();

        $workout->exercises()->attach($exercise->id, [
            'sets' => 3,
            'reps' => 10,
            'rest_seconds' => 60,
            'order' => 0,
        ]);

        $session = WorkoutSession::create([
            'workout_id' => $workout->id,
            'client_id' => $client->id,
            'started_at' => now()->subMinutes(45),
            'status' => 'in_progress',
        ]);

        $toggleAction = new ToggleExerciseCompletionAction;
        $toggleAction->execute($workout, $exercise, $user, $session->id);

        $action = new CompleteWorkoutAction;
        $result = $action->execute($workout, $user, $session);

        $this->assertTrue($result['success']);
        $this->assertEquals('Treino finalizado com sucesso!', $result['message']);

        $session->refresh();
        $this->assertEquals('completed', $session->status);
        $this->assertNotNull($session->completed_at);
        $this->assertNotNull($session->duration_minutes);
    }

    public function test_cannot_complete_session_twice(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $client = Client::factory()->create(['user_id' => $user->id]);
        $workout = Workout::factory()->create(['client_id' => $client->id]);
        $exercise = Exercise::factory()->create();

        $workout->exercises()->attach($exercise->id, [
            'sets' => 3,
            'reps' => 10,
            'rest_seconds' => 60,
            'order' => 0,
        ]);

        $session = WorkoutSession::create([
            'workout_id' => $workout->id,
            'client_id' => $client->id,
            'started_at' => now()->subMinutes(45),
            'completed_at' => now(),
            'duration_minutes' => 45,
            'status' => 'completed',
        ]);

        $action = new CompleteWorkoutAction;
        $result = $action->execute($workout, $user, $session);

        $this->assertFalse($result['success']);
        $this->assertEquals('Esta execução do treino já foi finalizada.', $result['message']);
    }

    public function test_cannot_complete_without_all_exercises(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $client = Client::factory()->create(['user_id' => $user->id]);
        $workout = Workout::factory()->create(['client_id' => $client->id]);
        $exercise1 = Exercise::factory()->create();
        $exercise2 = Exercise::factory()->create();

        $workout->exercises()->attach($exercise1->id, [
            'sets' => 3, 'reps' => 10, 'rest_seconds' => 60, 'order' => 0,
        ]);
        $workout->exercises()->attach($exercise2->id, [
            'sets' => 3, 'reps' => 10, 'rest_seconds' => 60, 'order' => 1,
        ]);

        $session = WorkoutSession::create([
            'workout_id' => $workout->id,
            'client_id' => $client->id,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        $toggleAction = new ToggleExerciseCompletionAction;
        $toggleAction->execute($workout, $exercise1, $user, $session->id);

        $action = new CompleteWorkoutAction;
        $result = $action->execute($workout, $user, $session);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Complete todos', $result['message']);
    }

    public function test_workout_remains_in_active_list_after_sessions(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $client = Client::factory()->create(['user_id' => $user->id]);
        $workout = Workout::factory()->create(['client_id' => $client->id]);

        WorkoutSession::create([
            'workout_id' => $workout->id,
            'client_id' => $client->id,
            'started_at' => now()->subHour(),
            'completed_at' => now(),
            'duration_minutes' => 60,
            'status' => 'completed',
        ]);

        $workout->refresh();

        $this->assertTrue($workout->is_active);
        $this->assertNull($workout->completed_at);
        $this->assertEquals(1, $workout->sessions()->count());
    }
}
