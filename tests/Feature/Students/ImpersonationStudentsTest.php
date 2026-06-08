<?php

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\Trainer;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => UserRole::ADMIN,
    ]);

    $this->trainerUser = User::factory()->create([
        'role' => UserRole::PERSONAL,
    ]);

    $this->trainer = Trainer::factory()->create([
        'user_id' => $this->trainerUser->id,
    ]);

    $this->studentUser = User::factory()->create([
        'role' => UserRole::CLIENT,
        'trainer_id' => $this->trainerUser->id,
    ]);

    $this->client = Client::factory()->create([
        'user_id' => $this->studentUser->id,
    ]);
});

test('trainer can access students page', function () {
    actingAs($this->trainerUser);

    get(route('students'))
        ->assertOk();
});

test('admin can access students page', function () {
    actingAs($this->admin);

    get(route('students'))
        ->assertOk();
});

test('admin can impersonate trainer and access students page', function () {
    actingAs($this->admin);

    $this->post(route('trainers.impersonate', $this->trainer));

    $this->assertTrue(session()->has('impersonating'));
    $this->assertEquals($this->admin->id, session('impersonator_id'));

    $this->assertEquals($this->trainerUser->id, auth()->id());

    get(route('students'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('students/ListStudents')
        );
});

test('admin cannot access admin students page while impersonating trainer', function () {
    actingAs($this->admin);

    $this->post(route('trainers.impersonate', $this->trainer));

    get(route('admin.students'))
        ->assertForbidden();
});

test('impersonated trainer sees only their own students', function () {
    $otherTrainerUser = User::factory()->create([
        'role' => UserRole::PERSONAL,
    ]);

    $otherClientUser = User::factory()->create([
        'role' => UserRole::CLIENT,
        'trainer_id' => $otherTrainerUser->id,
    ]);

    Client::factory()->create([
        'user_id' => $otherClientUser->id,
    ]);

    actingAs($this->admin);

    $this->post(route('trainers.impersonate', $this->trainer));

    $response = get(route('students'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('students', 1)
    );
});
