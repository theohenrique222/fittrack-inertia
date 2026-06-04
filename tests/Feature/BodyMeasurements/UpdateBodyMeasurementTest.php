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
        'weight' => 70,
        'height' => 170,
        'activity_level' => 'moderate',
        'goal' => 'maintain',
    ]);
});

test('unauthenticated users cannot update measurements', function () {
    $response = $this->put(route('students.measurements.update', [
        'student' => $this->client,
        'measurement' => $this->measurement,
    ]), [
        'weight' => 75,
        'height' => 175,
        'activity_level' => 'active',
        'goal' => 'gain',
    ]);

    $response->assertRedirect(route('login'));
});

test('admin can update any measurement', function () {
    actingAs($this->admin);

    $response = $this->put(route('students.measurements.update', [
        'student' => $this->client,
        'measurement' => $this->measurement,
    ]), [
        'weight' => 75.5,
        'height' => 175,
        'neck' => 36,
        'waist' => 82,
        'activity_level' => 'active',
        'goal' => 'gain',
    ]);

    $response->assertRedirect(route('students.measurements', $this->client));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('body_measurements', [
        'id' => $this->measurement->id,
        'weight' => 75.5,
        'height' => 175,
        'neck' => 36,
        'waist' => 82,
    ]);
});

test('student can update own measurement', function () {
    actingAs($this->studentUser);

    $response = $this->put(route('students.measurements.update', [
        'student' => $this->client,
        'measurement' => $this->measurement,
    ]), [
        'weight' => 68,
        'height' => 170,
        'activity_level' => 'moderate',
        'goal' => 'lose',
    ]);

    $response->assertRedirect(route('students.measurements', $this->client));

    $this->assertDatabaseHas('body_measurements', [
        'id' => $this->measurement->id,
        'weight' => 68,
        'goal' => 'lose',
    ]);
});

test('student cannot update measurement from another student', function () {
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

    $response = $this->put(route('students.measurements.update', [
        'student' => $otherClient,
        'measurement' => $otherMeasurement,
    ]), [
        'weight' => 75,
        'height' => 175,
        'activity_level' => 'moderate',
        'goal' => 'maintain',
    ]);

    $response->assertForbidden();
});

test('returns 404 when measurement does not belong to student', function () {
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

    $response = $this->put(route('students.measurements.update', [
        'student' => $this->client,
        'measurement' => $otherMeasurement,
    ]), [
        'weight' => 75,
        'height' => 175,
        'activity_level' => 'moderate',
        'goal' => 'maintain',
    ]);

    $response->assertNotFound();
});

test('validates required fields on update', function () {
    actingAs($this->admin);

    $response = $this->put(route('students.measurements.update', [
        'student' => $this->client,
        'measurement' => $this->measurement,
    ]), []);

    $response->assertSessionHasErrors(['weight', 'height', 'activity_level', 'goal']);
});

test('includes caution warnings on update for unusual measurements', function () {
    actingAs($this->admin);

    $response = $this->put(route('students.measurements.update', [
        'student' => $this->client,
        'measurement' => $this->measurement,
    ]), [
        'weight' => 310,
        'height' => 170,
        'activity_level' => 'moderate',
        'goal' => 'maintain',
    ]);

    $response->assertRedirect(route('students.measurements', $this->client));
    $response->assertSessionHas('measurement_warnings');
});
