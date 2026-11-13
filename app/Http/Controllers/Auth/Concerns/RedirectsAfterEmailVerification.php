<?php

namespace App\Http\Controllers\Auth\Concerns;

use App\Models\User;

trait RedirectsAfterEmailVerification
{
    /**
     * Trang mặc định sau khi xác thực email: tài khoản mới vào trang Welcome, còn lại vào trang chính theo vai trò.
     */
    protected function homeAfterVerification(User $user): string
    {
        return $user->onboarding_completed
            ? route($user->dashboardRouteName(), absolute: false)
            : route('onboarding.welcome', absolute: false);
    }
}
