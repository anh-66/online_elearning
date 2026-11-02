<section>
    <header class="border-b border-neutral-100 dark:border-neutral-800 pb-5">
        <p class="text-sm font-semibold uppercase tracking-[0.14em] text-brand-700 dark:text-brand-400">Bảo mật</p>
        <h2 class="mt-2 text-xl font-semibold tracking-tight text-neutral-900 dark:text-neutral-100">{{ __('Đổi mật khẩu') }}</h2>

        <p class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400">
            {{ __('Sử dụng mật khẩu mạnh và không chia sẻ với người khác.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Mật khẩu hiện tại" class="text-sm font-semibold text-neutral-700 dark:text-neutral-200" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-2 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 px-4 py-3 text-sm shadow-sm focus:border-brand-600 focus:ring-brand-600" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Mật khẩu mới" class="text-sm font-semibold text-neutral-700 dark:text-neutral-200" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-2 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 px-4 py-3 text-sm shadow-sm focus:border-brand-600 focus:ring-brand-600" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Xác nhận mật khẩu mới" class="text-sm font-semibold text-neutral-700 dark:text-neutral-200" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-2 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 px-4 py-3 text-sm shadow-sm focus:border-brand-600 focus:ring-brand-600" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-wrap items-center gap-4 border-t border-neutral-100 dark:border-neutral-800 pt-5">
            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-brand-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-600 focus:ring-offset-2">{{ __('Cập nhật mật khẩu') }}</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-brand-700 dark:text-brand-400"
                >Đã lưu.</p>
            @endif
        </div>
    </form>
</section>
