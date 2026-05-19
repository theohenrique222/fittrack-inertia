<?php

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\Exercise;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $this->trainer = User::factory()->create(['role' => UserRole::PERSONAL]);
    $this->clientUser = User::factory()->create(['role' => UserRole::CLIENT]);
    Client::factory()->create(['user_id' => $this->clientUser->id]);
});

test('guests cannot access reports page', function () {
    $response = $this->get(route('reports'));

    $response->assertRedirect(route('login'));
});

test('clients cannot access reports page', function () {
    $this->actingAs($this->clientUser);

    $response = $this->get(route('reports'));

    $response->assertForbidden();
});

test('admin can access reports page', function () {
    $this->actingAs($this->admin);

    $response = $this->get(route('reports'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('reports/Index'));
});

test('trainer can access reports page', function () {
    $this->actingAs($this->trainer);

    $response = $this->get(route('reports'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('reports/Index'));
});

test('admin reports returns correct overview data', function () {
    User::factory()->count(3)->create(['role' => UserRole::CLIENT]);
    $trainer = User::factory()->create(['role' => UserRole::PERSONAL]);
    Client::factory()->count(2)->create();
    Workout::factory()->count(4)->create(['trainer_id' => $trainer->id]);

    $this->actingAs($this->admin);

    $response = $this->get(route('reports', ['period' => '30d']));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('overview.totalUsers')
        ->has('overview.totalClients')
        ->has('overview.totalTrainers')
        ->has('overview.totalExercises')
        ->has('overview.totalWorkouts')
        ->has('usersByRole')
        ->has('registrationsOverTime')
        ->has('usersTable')
    );
});

test('trainer reports returns correct overview data', function () {
    $clientUser = User::factory()->create(['role' => UserRole::CLIENT, 'trainer_id' => $this->trainer->id]);
    $client = Client::factory()->create(['user_id' => $clientUser->id]);
    Workout::factory()->count(3)->create([
        'trainer_id' => $this->trainer->id,
        'client_id' => $client->id,
    ]);

    $this->actingAs($this->trainer);

    $response = $this->get(route('reports', ['period' => '30d']));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('overview.totalStudents')
        ->has('overview.activeStudents')
        ->has('overview.totalWorkouts')
        ->has('overview.exercisesUsed')
        ->has('studentsByStatus')
        ->has('workoutsOverTime')
        ->has('workoutVolume')
        ->has('studentsTable')
    );
});

test('admin reports returns users grouped by role', function () {
    User::factory()->count(2)->create(['role' => UserRole::CLIENT]);
    User::factory()->count(1)->create(['role' => UserRole::PERSONAL]);

    $this->actingAs($this->admin);

    $response = $this->get(route('reports'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('usersByRole', function ($roles) {
            $roleNames = collect($roles)->pluck('role')->all();
            expect($roleNames)->toContain('Administrador');

            return true;
        })
    );
});

test('trainer reports only shows own students', function () {
    $otherTrainer = User::factory()->create(['role' => UserRole::PERSONAL]);

    $myClient = User::factory()->create(['role' => UserRole::CLIENT, 'trainer_id' => $this->trainer->id]);
    Client::factory()->create(['user_id' => $myClient->id]);

    $otherClient = User::factory()->create(['role' => UserRole::CLIENT, 'trainer_id' => $otherTrainer->id]);
    Client::factory()->create(['user_id' => $otherClient->id]);

    $this->actingAs($this->trainer);

    $response = $this->get(route('reports'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('overview.totalStudents', 1)
    );
});

test('reports accept valid period filter', function () {
    $this->actingAs($this->admin);

    foreach (['7d', '30d', '90d', '12m'] as $period) {
        $response = $this->get(route('reports', ['period' => $period]));
        $response->assertOk();
    }
});

test('reports reject invalid period filter', function () {
    $this->actingAs($this->admin);

    $response = $this->get(route('reports', ['period' => 'invalid']));
    $response->assertSessionHasErrors('period');
});

test('admin reports includes most used exercises', function () {
    $exercise1 = Exercise::factory()->create(['name' => 'Supino Reto', 'muscle_group' => 'Peito', 'is_active' => true]);
    $exercise2 = Exercise::factory()->create(['name' => 'Agachamento', 'muscle_group' => 'Pernas', 'is_active' => true]);

    $client = Client::factory()->create();
    $workout1 = Workout::factory()->create(['client_id' => $client->id, 'trainer_id' => $this->admin->id]);
    $workout2 = Workout::factory()->create(['client_id' => $client->id, 'trainer_id' => $this->admin->id]);

    DB::table('exercise_workout')->insert([
        ['workout_id' => $workout1->id, 'exercise_id' => $exercise1->id, 'sets' => 4, 'reps' => 10, 'rest_seconds' => 60, 'order' => 1, 'created_at' => now(), 'updated_at' => now()],
        ['workout_id' => $workout1->id, 'exercise_id' => $exercise2->id, 'sets' => 3, 'reps' => 12, 'rest_seconds' => 90, 'order' => 2, 'created_at' => now(), 'updated_at' => now()],
        ['workout_id' => $workout2->id, 'exercise_id' => $exercise1->id, 'sets' => 4, 'reps' => 8, 'rest_seconds' => 60, 'order' => 1, 'created_at' => now(), 'updated_at' => now()],
    ]);

    $this->actingAs($this->admin);

    $response = $this->get(route('reports'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->has('mostUsedExercises'));
});

test('trainer reports includes workout volume data', function () {
    $clientUser = User::factory()->create(['role' => UserRole::CLIENT, 'trainer_id' => $this->trainer->id]);
    $client = Client::factory()->create(['user_id' => $clientUser->id]);
    $exercise = Exercise::factory()->create(['is_active' => true]);
    $workout = Workout::factory()->create([
        'trainer_id' => $this->trainer->id,
        'client_id' => $client->id,
    ]);

    DB::table('exercise_workout')->insert([
        ['workout_id' => $workout->id, 'exercise_id' => $exercise->id, 'sets' => 4, 'reps' => 10, 'rest_seconds' => 60, 'order' => 1, 'created_at' => now(), 'updated_at' => now()],
    ]);

    $this->actingAs($this->trainer);

    $response = $this->get(route('reports'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('workoutVolume.totalSets', 4)
        ->where('workoutVolume.totalReps', 10)
        ->where('workoutVolume.totalRestMinutes', 1)
        ->where('workoutVolume.totalVolume', 40)
    );
});
