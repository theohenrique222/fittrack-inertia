<?php

namespace App\Contexts\Profile\Actions;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateProfilePhotoAction
{
    public function execute(User $user, ?UploadedFile $photo): void
    {
        if ($photo === null) {
            return;
        }

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $path = $photo->store('profile-photos', 'public');

        $user->update(['profile_photo_path' => $path]);
    }
}
