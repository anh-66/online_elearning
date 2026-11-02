<section class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
    <header class="max-w-2xl">
        <p class="text-sm font-semibold uppercase tracking-[0.14em] text-rose-600 dark:text-rose-400">Vùng nguy hiểm</p>
        <h2 class="mt-2 text-xl font-semibold tracking-tight text-neutral-900 dark:text-neutral-100">{{ __('Xóa tài khoản') }}</h2>

        <p class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400">
            {{ __('Thao tác này sẽ xóa vĩnh viễn tài khoản và dữ liệu liên quan. Bạn sẽ cần xác nhận bằng mật khẩu.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="shrink-0 rounded-xl px-5 py-3"
    >{{ __('Xóa tài khoản') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-5 p-6 sm:p-8">
            @csrf
            @method('delete')

            <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                {{ __('Bạn có chắc chắn muốn xóa tài khoản?') }}
            </h2>

            <p class="text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                {{ __('Tất cả tài nguyên và dữ liệu của bạn sẽ bị xóa vĩnh viễn. Nhập mật khẩu để xác nhận thao tác này.') }}
            </p>

            <div>
                <x-input-label for="password" value="Mật khẩu" class="text-sm font-semibold text-neutral-700 dark:text-neutral-200" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-2 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 px-4 py-3 text-sm shadow-sm focus:border-brand-600 focus:ring-brand-600"
                    placeholder="Nhập mật khẩu"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="flex flex-wrap justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="rounded-xl px-4 py-2.5">
                    {{ __('Hủy') }}
                </x-secondary-button>

                <x-danger-button class="rounded-xl px-4 py-2.5">
                    {{ __('Xác nhận xóa') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
