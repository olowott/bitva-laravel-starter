<?php

namespace App\Services\Profile;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvatarService
{
    public function store(
        User $user,
        UploadedFile $file
    ): string {
        $extension = $file->extension();

        $filename = (string) Str::uuid();

        if ($extension) {
            $filename .= '.' . $extension;
        }

        $path = $file->storeAs(
            'avatars',
            $filename,
            'public_assets'
        );

        if (!$path) {
            throw new \RuntimeException(
                'The avatar could not be stored.'
            );
        }

        $oldAvatar = $user->avatar;

        $user->avatar = $path;
        $user->save();

        if ($oldAvatar) {
            Storage::disk('public_assets')
                ->delete($oldAvatar);
        }

        return $path;
    }

    public function remove(User $user): void
    {
        if ($user->avatar) {
            Storage::disk('public_assets')
                ->delete($user->avatar);
        }

        $user->avatar = null;
        $user->save();
    }
}
