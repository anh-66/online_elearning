<x-guest-layout title="Đăng nhập">
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-indigo-600">Chào mừng trở lại</p>
        <h2 class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ __('Đăng nhập') }}</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">Đăng nhập để tiếp tục hành trình học tập của bạn.</p>
    </div>

    <x-auth-session-status class="mb-5 rounded-md bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700" :status="session('status')" />

    @if (session('error'))
        <p class="mb-5 rounded-md bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{{ session('error') }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Địa chỉ email" class="text-sm font-semibold text-slate-700" />
            <x-text-input id="email" class="mt-2 block w-full rounded-md border-slate-300 px-4 py-3 text-sm shadow-none placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/15" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="ban@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between gap-3">
                <x-input-label for="password" value="Mật khẩu" class="text-sm font-semibold text-slate-700" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-indigo-600 transition hover:text-indigo-700" href="{{ route('password.request') }}">{{ __('Quên mật khẩu?') }}</a>
                @endif
            </div>
            <x-text-input id="password" class="mt-2 block w-full rounded-md border-slate-300 px-4 py-3 text-sm shadow-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/15" type="password" name="password" required autocomplete="current-password" placeholder="Nhập mật khẩu" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center gap-2.5 text-sm text-slate-600">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-none focus:ring-indigo-500" name="remember">
                <span>{{ __('Ghi nhớ đăng nhập') }}</span>
            </label>
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-indigo-600 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            {{ __('Đăng nhập') }}
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
        </button>
    </form>

    <div class="my-6 flex items-center gap-3 text-xs text-slate-400">
        <span class="h-px flex-1 bg-slate-200"></span>
        <span>{{ __('hoặc') }}</span>
        <span class="h-px flex-1 bg-slate-200"></span>
    </div>

    <a href="{{ route('auth.google') }}" class="inline-flex w-full items-center justify-center gap-3 rounded-md border border-slate-300 px-5 py-3.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
            <path fill="#4285F4" d="M21.35 12.23c0-.72-.06-1.42-.18-2.09H12v3.96h5.24a4.48 4.48 0 0 1-1.94 2.94v2.45h3.14c1.84-1.7 2.91-4.2 2.91-7.26Z"/>
            <path fill="#34A853" d="M12 21.67c2.63 0 4.84-.87 6.45-2.36l-3.14-2.45c-.87.58-1.98.93-3.31.93-2.54 0-4.7-1.72-5.47-4.03H3.28v2.53A9.74 9.74 0 0 0 12 21.67Z"/>
            <path fill="#FBBC05" d="M6.53 13.76a5.86 5.86 0 0 1 0-3.52V7.71H3.28a9.75 9.75 0 0 0 0 8.58l3.25-2.53Z"/>
            <path fill="#EA4335" d="M12 6.21c1.43 0 2.72.49 3.74 1.46l2.8-2.8C16.84 3.3 14.63 2.33 12 2.33a9.74 9.74 0 0 0-8.72 5.38l3.25 2.53C7.3 7.93 9.46 6.21 12 6.21Z"/>
        </svg>
        {{ __('Đăng nhập bằng Google') }}
    </a>

    <p class="mt-8 text-center text-sm text-slate-500">
        {{ __('Chưa có tài khoản?') }}
        <a href="{{ route('register') }}" class="font-semibold text-indigo-600 transition hover:text-indigo-700">{{ __('Đăng ký ngay') }}</a>
    </p>

    <p class="mt-4 text-center text-sm">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 font-semibold text-slate-500 transition hover:text-indigo-600">
            {{ __('Quay về trang chủ') }}
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path d="M5 12h14m-6-6 6 6-6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </a>
    </p>
</x-guest-layout>
