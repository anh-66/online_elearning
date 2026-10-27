<x-guest-layout title="Đăng nhập">
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-indigo-600">Chào mừng trở lại</p>
        <h2 class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ __('Đăng nhập') }}</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">Đăng nhập để tiếp tục hành trình học tập của bạn.</p>
    </div>

    <x-auth-session-status class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Địa chỉ email" class="text-sm font-semibold text-slate-700" />
            <x-text-input id="email" class="mt-2 block w-full rounded-xl border-slate-200 px-4 py-3 text-sm shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="ban@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between gap-3">
                <x-input-label for="password" value="Mật khẩu" class="text-sm font-semibold text-slate-700" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-indigo-600 transition hover:text-indigo-700" href="{{ route('password.request') }}">{{ __('Quên mật khẩu?') }}</a>
                @endif
            </div>
            <x-text-input id="password" class="mt-2 block w-full rounded-xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="password" name="password" required autocomplete="current-password" placeholder="Nhập mật khẩu" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center gap-2.5 text-sm text-slate-600">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span>{{ __('Ghi nhớ đăng nhập') }}</span>
            </label>
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            {{ __('Đăng nhập') }}
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500">
        {{ __('Chưa có tài khoản?') }}
        <a href="{{ route('register') }}" class="font-semibold text-indigo-600 transition hover:text-indigo-700">{{ __('Đăng ký ngay') }}</a>
    </p>
</x-guest-layout>
