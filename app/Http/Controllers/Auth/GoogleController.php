<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $googleId = $googleUser->getId();
            $email = $googleUser->getEmail();

            if (! $googleId || ! $email) {
                throw new \RuntimeException('Google did not return a valid identity.');
            }

            $user = User::where('google_id', $googleId)->first();

            if (! $user) {
                $user = User::where('email', $email)->first();

                if ($user && $user->google_id && $user->google_id !== $googleId) {
                    throw new \RuntimeException('The email is linked to another Google account.');
                }

                if ($user) {
                    $user->update(['google_id' => $googleId]);
                }
            }

            if (! $user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $email,
                    'google_id' => $googleId,
                    'password' => Str::random(32),
                    'role' => User::ROLE_STUDENT,
                    'status' => User::STATUS_ACTIVE,
                    'email_verified_at' => now(),
                ]);
            }

            if (! $user->isActive()) {
                throw new \RuntimeException('The user account is not active.');
            }

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended(route($user->dashboardRouteName(), absolute: false));

        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('login')->with('error', 'Đăng nhập bằng Google thất bại. Vui lòng thử lại!');
        }
    }
}
