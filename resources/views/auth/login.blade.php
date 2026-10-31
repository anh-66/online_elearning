<x-guest-layout title="Đăng nhập">
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-neutral-500">Chào mừng bạn trở lại</p>
        <h2 class="mt-2 text-2xl font-bold tracking-tight text-neutral-950">Đăng nhập</h2>
        <p class="mt-2 text-sm leading-6 text-neutral-500">Đăng nhập để tiếp tục hành trình học tập của bạn.</p>
    </div>

    <x-auth-session-status class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm font-medium text-green-800" :status="session('status')" />

    @if (session('error'))
        <p class="mb-4 rounded-md bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{{ session('error') }}</p>
    @endif

    <a href="{{ route('auth.google') }}" class="inline-flex w-full items-center justify-center gap-3 rounded-md border border-neutral-300 px-5 py-3 text-sm font-medium text-neutral-900 shadow-sm transition hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2">
        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
            <path fill="#4285F4" d="M21.35 12.23c0-.72-.06-1.42-.18-2.09H12v3.96h5.24a4.48 4.48 0 0 1-1.94 2.94v2.45h3.14c1.84-1.7 2.91-4.2 2.91-7.26Z" />
            <path fill="#34A853" d="M12 21.67c2.63 0 4.84-.87 6.45-2.36l-3.14-2.45c-.87.58-1.98.93-3.31.93-2.54 0-4.7-1.72-5.47-4.03H3.28v2.53A9.74 9.74 0 0 0 12 21.67Z" />
            <path fill="#FBBC05" d="M6.53 13.76a5.86 5.86 0 0 1 0-3.52V7.71H3.28a9.75 9.75 0 0 0 0 8.58l3.25-2.53Z" />
            <path fill="#EA4335" d="M12 6.21c1.43 0 2.72.49 3.74 1.46l2.8-2.8C16.84 3.3 14.63 2.33 12 2.33a9.74 9.74 0 0 0-8.72 5.38l3.25 2.53C7.3 7.93 9.46 6.21 12 6.21Z" />
        </svg>
        Tiếp tục với Google
    </a>

    <div class="my-5 flex items-center gap-3 text-xs font-medium text-neutral-500">
        <span class="h-px flex-1 bg-neutral-200"></span>
        <span>HOẶC</span>
        <span class="h-px flex-1 bg-neutral-200"></span>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Địa chỉ email" class="text-sm font-medium text-neutral-800" />
            <x-text-input id="email" class="mt-2 block w-full border-neutral-300 px-4 py-3 text-sm placeholder:text-neutral-400 focus:border-green-700 focus:ring-green-700/20" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Nhập email của bạn" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex items-center justify-between gap-3">
                <x-input-label for="password" value="Mật khẩu" class="text-sm font-medium text-neutral-800" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-medium text-neutral-700 transition hover:text-green-700" href="{{ route('password.request') }}">Quên mật khẩu?</a>
                @endif
            </div>
            <x-text-input id="password" class="mt-2 block w-full border-neutral-300 px-4 py-3 text-sm placeholder:text-neutral-400 focus:border-green-700 focus:ring-green-700/20" type="password" name="password" required autocomplete="current-password" placeholder="Nhập mật khẩu" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-neutral-700">
            <input id="remember_me" type="checkbox" class="rounded border-neutral-300 text-green-700 shadow-none focus:ring-green-700" name="remember">
            <span>Ghi nhớ đăng nhập</span>
        </label>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-green-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-700 focus:ring-offset-2">
            Đăng nhập
        </button>
    </form>
</x-guest-layout>
