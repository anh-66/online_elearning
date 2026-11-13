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
            $avatarUrl = $googleUser->getAvatar();

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

            $isNewUser = false;
            if (! $user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $email,
                    'google_id' => $googleId,
                    'password' => Str::random(32),
                    'avatar' => $avatarUrl,
                    'role' => User::ROLE_STUDENT,
                    'status' => User::STATUS_ACTIVE,
                    'onboarding_completed' => false,
                ]);
                $isNewUser = true;
            }

            if (! $user->isActive()) {
                throw new \RuntimeException('The user account is not active.');
            }

            $this->markGoogleEmailAsVerified($user, $isNewUser);

            $this->syncGoogleAvatar($user, $avatarUrl);

            Auth::login($user);
            $request->session()->regenerate();

            if ($isNewUser) {
                return redirect()->route('onboarding.welcome');
            }

            if ($user->isAdmin()) {
                $intended = $request->session()->pull('url.intended');
                if ($intended && str_contains($intended, '/admin')) {
                    return redirect()->to($intended);
                }

                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended(route($user->dashboardRouteName(), absolute: false));

        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('login')->with('error', 'Đăng nhập bằng Google thất bại. Vui lòng thử lại!');
        }
    }

    /**
     * Google đã xác minh email nên không cần mã OTP. Tài khoản đăng ký bằng mật khẩu mà chưa xác thực
     * thì đổi mật khẩu sang ngẫu nhiên, để người đăng ký trước bằng email của người khác không dùng chung được.
     */
    private function markGoogleEmailAsVerified(User $user, bool $isNewUser): void
    {
        if ($user->email_verified_at !== null) {
            return;
        }

        $attributes = ['email_verified_at' => now()];
        if (! $isNewUser) {
            $attributes['password'] = Str::random(32);
        }

        $user->forceFill($attributes)->save();
    }

    /**
     * Use the Google profile picture unless the user has their own uploaded avatar.
     */
    private function syncGoogleAvatar(User $user, ?string $avatarUrl): void
    {
        if (! is_string($avatarUrl) || $avatarUrl === '' || $user->avatar === $avatarUrl) {
            return;
        }

        $hasExternalAvatar = is_string($user->avatar) && str_starts_with($user->avatar, 'http');

        if (! $user->hasAvatar() || $hasExternalAvatar) {
            $user->update(['avatar' => $avatarUrl]);
        }
    }
}
