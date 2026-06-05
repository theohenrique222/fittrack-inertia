<?php

use App\Actions\Students\DestroyStudentAction;
use App\Actions\Students\ForceDeleteStudentAction;
use App\Actions\Students\RestoreStudentAction;
use App\Enums\UserRole;
use App\Models\Client;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => UserRole::ADMIN,
    ]);

    $this->studentUser = User::factory()->create([
        'role' => UserRole::CLIENT,
        'trainer_id' => $this->admin->id,
    ]);

    $this->client = Client::factory()->create([
        'user_id' => $this->studentUser->id,
    ]);
});

test('destroy action soft deletes client and user', function () {
    $action = app(DestroyStudentAction::class);
    $action->execute($this->client);

    $this->client->refresh();
    $this->studentUser->refresh();

    expect($this->client->trashed())->toBeTrue()
        ->and($this->studentUser->trashed())->toBeTrue();
});

test('soft deleted client is not returned in default queries', function () {
    $action = app(DestroyStudentAction::class);
    $action->execute($this->client);

    $activeClients = Client::with('user')->get();

    expect($activeClients)->not->toContain($this->client);
});

test('soft deleted user cannot log in', function () {
    $action = app(DestroyStudentAction::class);
    $action->execute($this->client);

    $this->studentUser->refresh();

    expect($this->studentUser->trashed())->toBeTrue();
});

test('restore action restores client and user', function () {
    $action = app(DestroyStudentAction::class);
    $action->execute($this->client);

    $restoreAction = app(RestoreStudentAction::class);
    $restoreAction->execute($this->client);

    $this->client->refresh();
    $this->studentUser->refresh();

    expect($this->client->trashed())->toBeFalse()
        ->and($this->studentUser->trashed())->toBeFalse();
});

test('restored client appears in default queries again', function () {
    $action = app(DestroyStudentAction::class);
    $action->execute($this->client);

    $restoreAction = app(RestoreStudentAction::class);
    $restoreAction->execute($this->client);

    $activeClients = Client::with('user')->get();

    expect($activeClients->pluck('id'))->toContain($this->client->id);
});

test('force delete action permanently deletes client and user', function () {
    $action = app(DestroyStudentAction::class);
    $action->execute($this->client);

    $forceAction = app(ForceDeleteStudentAction::class);
    $forceAction->execute($this->client);

    expect(Client::withTrashed()->find($this->client->id))->toBeNull()
        ->and(User::withTrashed()->find($this->studentUser->id))->toBeNull();
});

test('unauthenticated users cannot restore students', function () {
    $action = app(DestroyStudentAction::class);
    $action->execute($this->client);

    $response = $this->post(route('students.restore', $this->client));

    $response->assertRedirect(route('login'));
});

test('unauthenticated users cannot force delete students', function () {
    $action = app(DestroyStudentAction::class);
    $action->execute($this->client);

    $response = $this->delete(route('students.force-delete', $this->client));

    $response->assertRedirect(route('login'));
});

test('destroy endpoint archives student via http', function () {
    actingAs($this->admin);

    $response = $this->delete(route('students.destroy', $this->client));

    $response->assertOk();

    $this->client->refresh();

    expect($this->client->trashed())->toBeTrue();
});

test('admin can restore student via http', function () {
    $action = app(DestroyStudentAction::class);
    $action->execute($this->client);

    actingAs($this->admin);

    $response = $this->post(route('students.restore', $this->client));

    $response->assertOk();

    $this->client->refresh();

    expect($this->client->trashed())->toBeFalse();
});

test('admin can force delete student via http', function () {
    $action = app(DestroyStudentAction::class);
    $action->execute($this->client);

    actingAs($this->admin);

    $response = $this->delete(route('students.force-delete', $this->client));

    expect(Client::withTrashed()->find($this->client->id))->toBeNull();
});

test('archived students not shown in admin list', function () {
    $action = app(DestroyStudentAction::class);
    $action->execute($this->client);

    actingAs($this->admin);

    $response = $this->get(route('admin.students'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->missing('students.0')
    );
});

test('archived students are listed in archived prop', function () {
    $action = app(DestroyStudentAction::class);
    $action->execute($this->client);

    actingAs($this->admin);

    $response = $this->get(route('admin.students'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('archivedStudents', 1)
    );
});

test('show route returns 404 for archived student', function () {
    $action = app(DestroyStudentAction::class);
    $action->execute($this->client);

    actingAs($this->admin);

    $response = $this->get(route('students.show', $this->client));

    $response->assertNotFound();
});
