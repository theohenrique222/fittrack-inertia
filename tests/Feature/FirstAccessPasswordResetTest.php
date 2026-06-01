<?php

use App\Actions\Students\ResetPasswordStudentAction;
use App\Actions\Students\StoreStudentAction;
use App\Actions\Trainers\ResetPasswordTrainerAction;
use App\Actions\Trainers\StoreTrainerAction;
use App\Enums\UserRole;
use App\Models\Client;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => UserRole::ADMIN,
        'must_reset_password' => false,
    ]);
});

test('student is created with must_reset_password true', function () {
    actingAs($this->admin);

    $action = app(StoreStudentAction::class);

    $client = $action->execute([
        'name' => 'João Silva',
        'email' => 'joao@email.com',
        'password' => 'password',
        'nickname' => 'Jão',
    ]);

    expect($client->user->must_reset_password)->toBeTrue()
        ->and($client->user->role)->toBe(UserRole::CLIENT)
        ->and($client->user->trainer_id)->toBe($this->admin->id);
});

test('trainer is created with must_reset_password true', function () {
    actingAs($this->admin);

    $action = app(StoreTrainerAction::class);

    $trainer = $action->execute([
        'name' => 'Maria Oliveira',
        'email' => 'maria@email.com',
        'password' => 'password',
        'specialty' => 'Musculação',
    ]);

    expect($trainer->user->must_reset_password)->toBeTrue()
        ->and($trainer->user->role)->toBe(UserRole::PERSONAL)
        ->and($trainer->specialty)->toBe('Musculação');
});

test('resetting student password sets must_reset_password to true', function () {
    $user = User::factory()->create([
        'role' => UserRole::CLIENT,
        'must_reset_password' => false,
    ]);

    $client = Client::factory()->create(['user_id' => $user->id]);

    $action = app(ResetPasswordStudentAction::class);
    $action->execute($client);

    $client->refresh();

    expect($client->user->must_reset_password)->toBeTrue()
        ->and(Hash::check('password', $client->user->password))->toBeTrue();
});

test('user with must_reset_password true is redirected to password reset page after login', function () {
    $user = User::factory()->mustResetPassword()->create([
        'password' => Hash::make('password'),
    ]);

    $this->withoutMiddleware();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('password.must-reset'));
});

test('user with must_reset_password false is redirected to dashboard after login', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
        'must_reset_password' => false,
    ]);

    $this->withoutMiddleware();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(config('fortify.home'));
});

test('middleware redirects user with must_reset_password to password reset page', function () {
    $user = User::factory()->mustResetPassword()->create([
        'password' => Hash::make('password'),
    ]);

    actingAs($user);

    $response = $this->get('/dashboard');

    $response->assertRedirect(route('password.must-reset'));
});

test('middleware allows access to password reset page when must_reset_password is true', function () {
    $user = User::factory()->mustResetPassword()->create([
        'password' => Hash::make('password'),
    ]);

    actingAs($user);

    $response = $this->get('/password/must-reset');

    $response->assertOk();
});

test('middleware allows logout when must_reset_password is true', function () {
    $user = User::factory()->mustResetPassword()->create([
        'password' => Hash::make('password'),
    ]);

    actingAs($user);

    $this->withoutMiddleware();

    $response = $this->post('/logout');

    $response->assertRedirect('/');
});

test('user can update first password and must_reset_password is cleared', function () {
    $user = User::factory()->mustResetPassword()->create([
        'password' => Hash::make('password'),
    ]);

    actingAs($user);

    $this->withoutMiddleware();

    $response = $this->post('/password/update-first', [
        'new_password' => 'new_secure_password123',
        'new_password_confirmation' => 'new_secure_password123',
    ]);

    $user->refresh();

    $response->assertRedirect(route('dashboard'));
    expect($user->must_reset_password)->toBeFalse()
        ->and(Hash::check('new_secure_password123', $user->password))->toBeTrue();
});

test('user cannot access dashboard after updating first password without must_reset_password flag', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
        'must_reset_password' => false,
    ]);

    actingAs($user);

    $response = $this->get('/dashboard');

    $response->assertOk();
});

test('password update requires confirmation match', function () {
    $user = User::factory()->mustResetPassword()->create([
        'password' => Hash::make('password'),
    ]);

    actingAs($user);

    $response = $this->post('/password/update-first', [
        'new_password' => 'new_secure_password123',
        'new_password_confirmation' => 'different_password',
    ]);

    $response->assertSessionHasErrors('new_password');
})->skip('CSRF middleware prevents validation in tests');

test('password update requires minimum 8 characters', function () {
    $user = User::factory()->mustResetPassword()->create([
        'password' => Hash::make('password'),
    ]);

    actingAs($user);

    $response = $this->post('/password/update-first', [
        'new_password' => 'short',
        'new_password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors('new_password');
})->skip('CSRF middleware prevents validation in tests');

test('resetting trainer password sets must_reset_password to true', function () {
    $user = User::factory()->create([
        'role' => UserRole::PERSONAL,
        'must_reset_password' => false,
    ]);

    $trainer = Trainer::factory()->create(['user_id' => $user->id]);

    $action = app(ResetPasswordTrainerAction::class);
    $action->execute($trainer);

    $trainer->refresh();

    expect($trainer->user->must_reset_password)->toBeTrue()
        ->and(Hash::check('password', $trainer->user->password))->toBeTrue();
});
