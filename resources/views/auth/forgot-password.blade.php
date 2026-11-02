<x-guest-layout title="Khôi phục mật khẩu">
    <div class="mb-8">
        <a href="{{ route('login') }}" class="mb-7 inline-flex items-center gap-2 text-sm font-semibold text-neutral-500 dark:text-neutral-400 transition hover:text-brand-700 dark:hover:text-brand-300">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m15 18-6-6 6-6M9 12h12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
            {{ __('Quay lại đăng nhập') }}
        </a>
        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-lg bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400">
            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Zm10-10V7a4 4 0 0 0-8 0v4h8Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg>
        </div>
        <h2 class="text-2xl font-bold tracking-tight text-neutral-950 dark:text-neutral-100">{{ __('Quên mật khẩu?') }}</h2>
        <p class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400">Đừng lo. Hãy nhập email đã đăng ký, chúng tôi sẽ gửi liên kết để bạn tạo mật khẩu mới.</p>
    </div>

    <x-auth-session-status class="mb-5 rounded-md bg-brand-50 dark:bg-brand-500/10 px-4 py-3 text-sm font-medium text-brand-800 dark:text-brand-300" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Địa chỉ email" class="text-sm font-medium text-neutral-800 dark:text-neutral-100" />
            <x-text-input id="email" class="mt-2 block w-full rounded-md border-neutral-300 dark:border-neutral-700 px-4 py-3 text-sm shadow-none focus:border-brand-700 focus:ring-brand-700/20" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" placeholder="ban@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-brand-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-700 focus:ring-offset-2">
            {{ __('Gửi liên kết đặt lại mật khẩu') }}
        </button>
    </form>
</x-guest-layout>
