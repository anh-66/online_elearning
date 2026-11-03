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

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5" enctype="multipart/form-data"
          x-data="{ avatarPickerOpen: false, avatarMode: 'options', avatarGenerated: @js(old('avatar_generated')), avatarPreview: @js($user->avatar ? asset('storage/'.$user->avatar) : null) }"
          @message.window="if ($event.origin === window.location.origin && $event.data?.type === 'avatar-ready' && typeof $event.data.dataUrl === 'string') { avatarGenerated = $event.data.dataUrl; avatarPreview = avatarGenerated; avatarPickerOpen = false; avatarMode = 'options'; $nextTick(() => $el.requestSubmit()); }">
        @csrf
        @method('patch')

        <div class="flex flex-col gap-4 rounded-2xl bg-neutral-50 dark:bg-neutral-800/40 p-4 sm:flex-row sm:items-center">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-brand-100 dark:bg-brand-500/15 text-xl font-semibold text-brand-800 dark:text-brand-300">
                <template x-if="avatarPreview">
                    <img :src="avatarPreview" alt="Ảnh đại diện mới" class="h-full w-full object-cover">
                </template>
                <template x-if="!avatarPreview">
                    <span>{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                </template>
            </div>
            <div class="min-w-0 flex-1">
                <x-input-label for="avatar" value="Ảnh đại diện" class="text-sm font-semibold text-neutral-700 dark:text-neutral-200" />
                <input id="avatar" x-ref="avatarInput" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only"
                       @change="avatarGenerated = ''; if ($event.target.files[0]) { const reader = new FileReader(); reader.onload = () => avatarPreview = reader.result; reader.readAsDataURL($event.target.files[0]); }">
                <input type="hidden" name="avatar_generated" x-model="avatarGenerated">
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" @click="avatarPickerOpen = true; avatarMode = 'options'"
                            class="inline-flex items-center gap-2 rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-600 focus:ring-offset-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"/></svg>
                        Tùy chỉnh ảnh đại diện
                    </button>
                    <span x-show="avatarGenerated" x-cloak class="text-xs font-medium text-brand-700 dark:text-brand-400">Đã chọn avatar thiết kế</span>
                </div>
                <p class="mt-2 text-xs text-neutral-500 dark:text-neutral-400">JPG, PNG hoặc WebP · tối đa 2 MB</p>
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                <x-input-error class="mt-2" :messages="$errors->get('avatar_generated')" />
            </div>
        </div>

        <template x-teleport="body">
        <div x-show="avatarPickerOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[9999] flex items-start justify-center overflow-y-auto bg-black/65 p-3 backdrop-blur-[2px] sm:p-5" @keydown.escape.window="avatarPickerOpen = false">
            <div class="my-auto flex max-h-[calc(100vh-2rem)] w-full max-w-5xl flex-col overflow-hidden rounded-lg bg-white shadow-2xl dark:bg-neutral-900" @click.stop>
                <div class="flex shrink-0 items-center justify-between border-b border-neutral-200 px-5 py-4 dark:border-neutral-800">
                    <div>
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Tùy chỉnh ảnh đại diện</h3>
                        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Chọn ảnh từ thiết bị hoặc thiết kế avatar riêng.</p>
                    </div>
                    <button type="button" @click="avatarPickerOpen = false" class="rounded-full p-2 text-neutral-500 transition hover:bg-neutral-100 hover:text-neutral-900 dark:hover:bg-neutral-800 dark:hover:text-white" aria-label="Đóng">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 6 12 12M6 18 18 6"/></svg>
                    </button>
                </div>

                <div x-show="avatarMode === 'options'" class="grid gap-4 p-5 sm:grid-cols-2 sm:p-8">
                    <button type="button" @click="$refs.avatarInput.value = ''; avatarGenerated = ''; $refs.avatarInput.click(); avatarPickerOpen = false"
                            class="group rounded-2xl border border-neutral-200 p-6 text-left transition hover:border-brand-500 hover:bg-brand-50 dark:border-neutral-700 dark:hover:border-brand-500 dark:hover:bg-brand-950/30">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-500/15 dark:text-brand-300">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16.5V19a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-2.5M12 3v11m0 0 4-4m-4 4-4-4"/></svg>
                        </span>
                        <span class="mt-4 block text-base font-semibold text-neutral-900 dark:text-white">Tải ảnh lên</span>
                        <span class="mt-1 block text-sm text-neutral-500 dark:text-neutral-400">Chọn JPG, PNG hoặc WebP từ thiết bị.</span>
                    </button>
                    <button type="button" @click="$refs.avatarInput.value = ''; avatarGenerated = ''; avatarMode = 'designer'"
                            class="group rounded-2xl border border-neutral-200 p-6 text-left transition hover:border-brand-500 hover:bg-brand-50 dark:border-neutral-700 dark:hover:border-brand-500 dark:hover:bg-brand-950/30">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-500/15 dark:text-brand-300">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v18M3 12h18M5.5 5.5l13 13m0-13-13 13"/></svg>
                        </span>
                        <span class="mt-4 block text-base font-semibold text-neutral-900 dark:text-white">Thiết kế avatar</span>
                        <span class="mt-1 block text-sm text-neutral-500 dark:text-neutral-400">Kết hợp kiểu tóc, khuôn mặt, quần áo và màu nền.</span>
                    </button>
                </div>

                <div x-show="avatarMode === 'designer'" x-cloak class="min-h-0 overflow-y-auto p-3 sm:p-5">
                    <button type="button" @click="avatarMode = 'options'" class="mb-3 inline-flex items-center gap-1 text-sm font-semibold text-brand-700 hover:text-brand-800 dark:text-brand-400">← Quay lại lựa chọn</button>
                    <iframe src="{{ route('avatar-generator') }}" title="Thiết kế avatar" class="block h-[460px] w-full rounded-lg border border-neutral-200 bg-white dark:border-neutral-700" loading="lazy"></iframe>
                </div>
            </div>
        </div>
        </template>

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
