<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\Profile\AvatarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(
        ProfileUpdateRequest $request,
        AvatarService $avatarService
    ): RedirectResponse {
        $user = $request->user();

        $user->fill(
            $request->safe()->except('avatar')
        );

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($request->hasFile('avatar')) {
            $avatarService->store(
                $user,
                $request->file('avatar')
            );
        }

        return Redirect::route('profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    public function destroyAvatar(
        Request $request,
        AvatarService $avatarService
    ): RedirectResponse {
        $avatarService->remove(
            $request->user()
        );

        return back()->with(
            'success',
            'Profile photo removed successfully.'
        );
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        abort_if(
            $user->hasRole('super_admin'),
            403,
            'The super administrator account cannot be deleted.'
        );

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
