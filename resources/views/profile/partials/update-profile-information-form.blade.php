<section>
    <header class="border-b border-neutral-100 dark:border-neutral-800 pb-5">
        <p class="text-sm font-semibold uppercase tracking-[0.14em] text-brand-700 dark:text-brand-400">Thông tin cá nhân</p>
        <h2 class="mt-2 text-xl font-semibold tracking-tight text-neutral-900 dark:text-neutral-100">{{ __('Cập nhật hồ sơ') }}</h2>

        <p class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400">
            {{ __('Chỉnh sửa thông tin hiển thị và ảnh đại diện của tài khoản.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="flex flex-col gap-4 rounded-2xl bg-neutral-50 dark:bg-neutral-800/40 p-4 sm:flex-row sm:items-center">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-brand-100 dark:bg-brand-500/15 text-xl font-semibold text-brand-800 dark:text-brand-300">
                @if ($user->avatar)
                    <img src="{{ asset('storage/'.$user->avatar) }}" alt="Ảnh đại diện của {{ $user->name }}" class="h-full w-full object-cover">
                @else
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <x-input-label for="avatar" value="Ảnh đại diện" class="text-sm font-semibold text-neutral-700 dark:text-neutral-200" />
                <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full cursor-pointer rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 text-sm text-neutral-600 dark:text-neutral-300 file:mr-4 file:border-0 file:bg-brand-50 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-brand-800 hover:file:bg-brand-100">
                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">JPG, PNG hoặc WebP · tối đa 2 MB</p>
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            </div>
        </div>

        <div>
            <x-input-label for="name" value="Họ và tên" class="text-sm font-semibold text-neutral-700 dark:text-neutral-200" />
            <x-text-input id="name" name="name" type="text" class="mt-2 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 px-4 py-3 text-sm shadow-sm focus:border-brand-600 focus:ring-brand-600" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Địa chỉ email" class="text-sm font-semibold text-neutral-700 dark:text-neutral-200" />
            <x-text-input id="email" name="email" type="email" class="mt-2 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 px-4 py-3 text-sm shadow-sm focus:border-brand-600 focus:ring-brand-600" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 p-3 text-sm leading-6 text-amber-800 dark:text-amber-300">
                        Địa chỉ email của bạn chưa được xác minh.

                        <button form="send-verification" class="ml-1 font-semibold underline underline-offset-2 hover:text-amber-950 focus:outline-none focus:ring-2 focus:ring-brand-600">
                            Bấm vào đây để gửi lại email xác minh.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-semibold text-brand-700 dark:text-brand-400">
                            Một liên kết xác minh mới đã được gửi tới email của bạn.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="bio" value="Giới thiệu" class="text-sm font-semibold text-neutral-700 dark:text-neutral-200" />
            <textarea id="bio" name="bio" rows="4" class="mt-2 block w-full rounded-xl border-neutral-200 dark:border-neutral-800 px-4 py-3 text-sm shadow-sm focus:border-brand-600 focus:ring-brand-600">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div class="flex flex-wrap items-center gap-4 border-t border-neutral-100 dark:border-neutral-800 pt-5">
            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-brand-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-600 focus:ring-offset-2">{{ __('Lưu thay đổi') }}</button>

            @if (session('status') === 'profile-updated')
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
