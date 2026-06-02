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

test('unauthenticated users cannot access latest measurement', function () {
    $response = $this->get(route('students.measurements.latest', $this->client));

    $response->assertRedirect(route('login'));
});

test('returns latest measurement data', function () {
    BodyMeasurement::factory()->create([
        'client_id' => $this->client->id,
        'weight' => 65.5,
        'height' => 165,
        'recorded_at' => now()->subDay(),
    ]);

    BodyMeasurement::factory()->create([
        'client_id' => $this->client->id,
        'weight' => 64.0,
        'height' => 165,
        'recorded_at' => now(),
    ]);

    actingAs($this->user);

    $response = $this->getJson(route('students.measurements.latest', $this->client));

    $response->assertOk()
        ->assertJson([
            'data' => [
                'weight' => 64.0,
                'height' => 165,
            ],
        ])
        ->assertJsonStructure([
            'data' => [
                'id',
                'recorded_at',
                'weight',
                'height',
                'metrics' => ['bmi', 'body_fat', 'lean_mass', 'bmr', 'macros'],
            ],
        ]);
});

test('returns null when no measurements exist', function () {
    actingAs($this->user);

    $response = $this->getJson(route('students.measurements.latest', $this->client));

    $response->assertOk();
    $response->assertJson(['data' => null]);
});

test('student can access own latest measurement', function () {
    BodyMeasurement::factory()->create([
        'client_id' => $this->client->id,
        'weight' => 70,
        'height' => 170,
    ]);

    actingAs($this->client->user);

    $response = $this->getJson(route('students.measurements.latest', $this->client));

    $response->assertOk();
});
