<?php

use App\Enums\UserRole;
use App\Models\BodyMeasurement;
use App\Models\Client;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => UserRole::ADMIN,
        'gender' => 'male',
        'birthdate' => '1990-01-01',
    ]);

    $this->studentUser = User::factory()->create([
        'role' => UserRole::CLIENT,
        'gender' => 'female',
        'birthdate' => '1995-05-15',
    ]);

    $this->client = Client::factory()->create([
        'user_id' => $this->studentUser,
    ]);

    $this->measurement = BodyMeasurement::factory()->create([
        'client_id' => $this->client->id,
    ]);
});

test('unauthenticated users cannot delete measurements', function () {
    $response = $this->delete(route('students.measurements.destroy', [
        'student' => $this->client,
        'measurement' => $this->measurement,
    ]));

    $response->assertRedirect(route('login'));
});

test('admin can delete any measurement', function () {
    actingAs($this->admin);

    $response = $this->delete(route('students.measurements.destroy', [
        'student' => $this->client,
        'measurement' => $this->measurement,
    ]));

    $response->assertRedirect(route('students.measurements', $this->client));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('body_measurements', [
        'id' => $this->measurement->id,
    ]);
});

test('student can delete own measurement', function () {
    actingAs($this->studentUser);

    $response = $this->delete(route('students.measurements.destroy', [
        'student' => $this->client,
        'measurement' => $this->measurement,
    ]));

    $response->assertRedirect(route('students.measurements', $this->client));
    $this->assertDatabaseMissing('body_measurements', [
        'id' => $this->measurement->id,
    ]);
});

test('student cannot delete measurement from another student', function () {
    $otherUser = User::factory()->create([
        'role' => UserRole::CLIENT,
    ]);

    $otherClient = Client::factory()->create([
        'user_id' => $otherUser,
    ]);

    $otherMeasurement = BodyMeasurement::factory()->create([
        'client_id' => $otherClient->id,
    ]);

    actingAs($this->studentUser);

    $response = $this->delete(route('students.measurements.destroy', [
        'student' => $otherClient,
        'measurement' => $otherMeasurement,
    ]));

    $response->assertForbidden();
});

test('returns 403 when measurement does not belong to student', function () {
    $otherUser = User::factory()->create([
        'role' => UserRole::CLIENT,
    ]);

    $otherClient = Client::factory()->create([
        'user_id' => $otherUser,
    ]);

    $otherMeasurement = BodyMeasurement::factory()->create([
        'client_id' => $otherClient->id,
    ]);

    actingAs($this->admin);

    $response = $this->delete(route('students.measurements.destroy', [
        'student' => $this->client,
        'measurement' => $otherMeasurement,
    ]));

    $response->assertNotFound();
});
