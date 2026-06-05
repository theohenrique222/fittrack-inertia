<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function createFakeImage(string $name = 'avatar.jpg'): UploadedFile
{
    $path = tempnam(sys_get_temp_dir(), 'test_').'.jpg';
    $jpegHeader = "\xFF\xD8\xFF\xE0".str_repeat("\x00", 1020);
    file_put_contents($path, $jpegHeader);

    return new UploadedFile($path, $name, 'image/jpeg', null, true);
}

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('profile.edit'));

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Test User',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->name)->toBe('Test User');
    expect($user->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    expect($user->fresh()->trashed())->toBeTrue();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('profile.edit'))
        ->delete(route('profile.destroy'), [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect(route('profile.edit'));

    expect($user->fresh())->not->toBeNull();
});

test('profile photo can be uploaded', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $file = createFakeImage();

    $response = $this
        ->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => $user->name,
            'profile_photo' => $file,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->profile_photo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($user->profile_photo_path);
});

test('profile photo can be removed', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $file = createFakeImage();
    $path = $file->store('profile-photos', 'public');
    $user->update(['profile_photo_path' => $path]);

    $response = $this
        ->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => $user->name,
            'remove_photo' => '1',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->profile_photo_path)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('profile photo is replaced when uploading a new one', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $oldFile = createFakeImage('old.jpg');
    $oldPath = $oldFile->store('profile-photos', 'public');
    $user->update(['profile_photo_path' => $oldPath]);

    $newFile = createFakeImage('new.jpg');

    $response = $this
        ->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => $user->name,
            'profile_photo' => $newFile,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->profile_photo_path)->not->toBe($oldPath);
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($user->profile_photo_path);
});
