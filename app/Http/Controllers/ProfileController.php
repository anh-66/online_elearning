<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->safe()->only(['name', 'email', 'bio']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('avatar')) {
            $previousAvatar = $user->avatar;
            $newAvatar = $request->file('avatar')->store('avatars', 'public');

            if (! is_string($newAvatar) || $newAvatar === '') {
                return back()->withErrors([
                    'avatar' => 'Không thể lưu ảnh avatar. Vui lòng thử lại.',
                ])->withInput();
            }

            $user->avatar = $newAvatar;

            if (is_string($previousAvatar) && $previousAvatar !== '') {
                Storage::disk('public')->delete($previousAvatar);
            }
        } elseif ($request->filled('avatar_generated')) {
            $previousAvatar = $user->avatar;
            $payload = (string) $request->string('avatar_generated');
            $encodedImage = substr($payload, strpos($payload, ',') + 1);
            $image = base64_decode($encodedImage, true);
            $path = 'avatars/'.Str::uuid().'.png';

            if ($image === false || ! Storage::disk('public')->put($path, $image)) {
                return back()->withErrors([
                    'avatar_generated' => 'Không thể lưu ảnh avatar. Vui lòng thử lại.',
                ])->withInput();
            }

            $user->avatar = $path;

            if (is_string($previousAvatar) && $previousAvatar !== '') {
                Storage::disk('public')->delete($previousAvatar);
            }
        }

        $user->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
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

        Auth::logout();

        if (is_string($user->avatar) && $user->avatar !== '') {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
