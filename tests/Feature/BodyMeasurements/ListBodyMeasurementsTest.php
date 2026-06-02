<?php

use App\Enums\UserRole;
use App\Models\BodyMeasurement;
use App\Models\Client;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => UserRole::ADMIN,
        'gender' => 'male',
        'birthdate' => '1990-01-01',
    ]);

    $this->client = Client::factory()->create([
        'user_id' => User::factory()->create([
            'role' => UserRole::CLIENT,
            'gender' => 'female',
            'birthdate' => '1995-05-15',
        ]),
    ]);
});

test('unauthenticated users cannot view measurements', function () {
    $response = $this->get(route('students.measurements', $this->client));

    $response->assertRedirect(route('login'));
});

test('displays measurements page with records', function () {
    BodyMeasurement::factory()->count(3)->create([
        'client_id' => $this->client->id,
    ]);

    actingAs($this->user);

    $response = $this->get(route('students.measurements', $this->client));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('students/BodyMeasurements')
        ->has('measurements.data', 3)
    );
});

test('displays measurements page without records', function () {
    actingAs($this->user);

    $response = $this->get(route('students.measurements', $this->client));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('students/BodyMeasurements')
        ->has('measurements.data', 0)
    );
});

test('student can view own measurements', function () {
    actingAs($this->client->user);

    $response = $this->get(route('students.measurements', $this->client));

    $response->assertOk();
});

test('includes profile completeness status', function () {
    actingAs($this->user);

    $response = $this->get(route('students.measurements', $this->client));

    $response->assertInertia(fn ($page) => $page
        ->where('user_has_profile', true)
    );
});

test('shows incomplete profile when user lacks gender', function () {
    $incompleteUser = User::factory()->create([
        'role' => UserRole::CLIENT,
        'gender' => null,
        'birthdate' => null,
    ]);

    $incompleteClient = Client::factory()->create([
        'user_id' => $incompleteUser,
    ]);

    actingAs($incompleteUser);

    $response = $this->get(route('students.measurements', $incompleteClient));

    $response->assertInertia(fn ($page) => $page
        ->where('user_has_profile', false)
    );
});
