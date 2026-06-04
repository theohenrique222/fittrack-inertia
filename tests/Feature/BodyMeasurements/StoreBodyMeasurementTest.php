<?php

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = User::factory()->create([
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

test('unauthenticated users cannot store measurements', function () {
    $response = $this->post(route('students.measurements.store', $this->client), [
        'weight' => 70,
        'height' => 170,
        'activity_level' => 'moderate',
        'goal' => 'maintain',
    ]);

    $response->assertRedirect(route('login'));
});

test('admin can store measurements for a student', function () {
    actingAs($this->admin);

    $response = $this->post(route('students.measurements.store', $this->client), [
        'weight' => 70.5,
        'height' => 170,
        'neck' => 35,
        'waist' => 80,
        'hip' => 95,
        'activity_level' => 'moderate',
        'goal' => 'maintain',
    ]);

    $response->assertRedirect(route('students.measurements', $this->client));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('body_measurements', [
        'client_id' => $this->client->id,
        'weight' => 70.5,
        'height' => 170,
    ]);
});

test('student can store their own measurements', function () {
    actingAs($this->client->user);

    $response = $this->post(route('students.measurements.store', $this->client), [
        'weight' => 65,
        'height' => 165,
        'activity_level' => 'active',
        'goal' => 'lose',
    ]);

    $response->assertRedirect(route('students.measurements', $this->client));
    $this->assertDatabaseHas('body_measurements', [
        'client_id' => $this->client->id,
        'weight' => 65,
    ]);
});

test('validates required fields', function () {
    actingAs($this->admin);

    $response = $this->post(route('students.measurements.store', $this->client), []);

    $response->assertSessionHasErrors(['weight', 'height', 'activity_level', 'goal']);
});

test('validates invalid activity level', function () {
    actingAs($this->admin);

    $response = $this->post(route('students.measurements.store', $this->client), [
        'weight' => 70,
        'height' => 170,
        'activity_level' => 'super_active',
        'goal' => 'maintain',
    ]);

    $response->assertSessionHasErrors(['activity_level']);
});

test('validates invalid goal', function () {
    actingAs($this->admin);

    $response = $this->post(route('students.measurements.store', $this->client), [
        'weight' => 70,
        'height' => 170,
        'activity_level' => 'moderate',
        'goal' => 'extreme',
    ]);

    $response->assertSessionHasErrors(['goal']);
});

test('validates weight range', function () {
    actingAs($this->admin);

    $response = $this->post(route('students.measurements.store', $this->client), [
        'weight' => 10,
        'height' => 170,
        'activity_level' => 'moderate',
        'goal' => 'maintain',
    ]);

    $response->assertSessionHasErrors(['weight']);
});

test('validates weight high range boundary', function () {
    actingAs($this->admin);

    $response = $this->post(route('students.measurements.store', $this->client), [
        'weight' => 701,
        'height' => 170,
        'activity_level' => 'moderate',
        'goal' => 'maintain',
    ]);

    $response->assertSessionHasErrors(['weight']);
});

test('accepts diverse body measurements within inclusive bounds', function () {
    actingAs($this->admin);

    $response = $this->post(route('students.measurements.store', $this->client), [
        'weight' => 300,
        'height' => 250,
        'neck' => 80,
        'waist' => 220,
        'hip' => 220,
        'chest' => 230,
        'thigh' => 120,
        'arm' => 80,
        'forearm' => 60,
        'calf' => 70,
        'shoulders' => 250,
        'activity_level' => 'moderate',
        'goal' => 'maintain',
    ]);

    $response->assertRedirect(route('students.measurements', $this->client));
    $this->assertDatabaseHas('body_measurements', [
        'client_id' => $this->client->id,
        'weight' => 300,
        'neck' => 80,
        'thigh' => 120,
        'arm' => 80,
    ]);
});

test('includes caution warnings for unusual measurements', function () {
    actingAs($this->admin);

    $response = $this->post(route('students.measurements.store', $this->client), [
        'weight' => 310,
        'height' => 170,
        'activity_level' => 'moderate',
        'goal' => 'maintain',
    ]);

    $response->assertRedirect(route('students.measurements', $this->client));
    $response->assertSessionHas('measurement_warnings');
});

test('does not include caution warnings for typical measurements', function () {
    actingAs($this->admin);

    $response = $this->post(route('students.measurements.store', $this->client), [
        'weight' => 70,
        'height' => 170,
        'activity_level' => 'moderate',
        'goal' => 'maintain',
    ]);

    $response->assertRedirect(route('students.measurements', $this->client));
    $response->assertSessionMissing('measurement_warnings');
});

test('student without gender cannot store measurements', function () {
    $incompleteUser = User::factory()->create([
        'role' => UserRole::CLIENT,
        'gender' => null,
        'birthdate' => null,
    ]);

    $incompleteClient = Client::factory()->create([
        'user_id' => $incompleteUser,
    ]);

    actingAs($incompleteUser);

    $response = $this->post(route('students.measurements.store', $incompleteClient), [
        'weight' => 70,
        'height' => 170,
        'activity_level' => 'moderate',
        'goal' => 'maintain',
    ]);

    $response->assertSessionHasErrors(['profile_incomplete']);
});
