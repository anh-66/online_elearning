<x-guest-layout title="Xác nhận mật khẩu">
    <div class="mb-6">
        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-lg bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400">
            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Zm10-10V7a4 4 0 0 0-8 0v4h8Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg>
        </div>
        <h2 class="text-2xl font-bold tracking-tight text-neutral-950 dark:text-neutral-100">Xác nhận mật khẩu</h2>
        <p class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400">Đây là khu vực bảo mật. Vui lòng nhập lại mật khẩu trước khi tiếp tục.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="password" value="Mật khẩu" class="text-sm font-medium text-neutral-800 dark:text-neutral-100" />
            <x-text-input id="password" class="mt-2 block w-full border-neutral-300 dark:border-neutral-700 px-4 py-3 text-sm placeholder:text-neutral-400 focus:border-brand-700 focus:ring-brand-700/20" type="password" name="password" required autocomplete="current-password" placeholder="Nhập mật khẩu hiện tại" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-brand-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-700 focus:ring-offset-2">
            Xác nhận
        </button>
    </form>
</x-guest-layout>
