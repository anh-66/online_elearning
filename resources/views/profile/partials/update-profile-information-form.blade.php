<section>
    <header class="border-b border-slate-100 pb-5">
        <p class="text-sm font-semibold uppercase tracking-[0.14em] text-emerald-700">Thông tin cá nhân</p>
        <h2 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">{{ __('Cập nhật hồ sơ') }}</h2>

        <p class="mt-2 text-sm leading-6 text-slate-500">
            {{ __('Chỉnh sửa thông tin hiển thị và ảnh đại diện của tài khoản.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="flex flex-col gap-4 rounded-2xl bg-slate-50 p-4 sm:flex-row sm:items-center">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-indigo-100 text-xl font-semibold text-indigo-700">
                @if ($user->avatar)
                    <img src="{{ asset('storage/'.$user->avatar) }}" alt="Ảnh đại diện của {{ $user->name }}" class="h-full w-full object-cover">
                @else
                    {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <x-input-label for="avatar" value="Ảnh đại diện" class="text-sm font-semibold text-slate-700" />
                <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full cursor-pointer rounded-xl border border-slate-200 bg-white text-sm text-slate-600 file:mr-4 file:border-0 file:bg-indigo-50 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-2 text-xs text-slate-500">JPG, PNG hoặc WebP · tối đa 2 MB</p>
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            </div>
        </div>

        <div>
            <x-input-label for="name" value="Họ và tên" class="text-sm font-semibold text-slate-700" />
            <x-text-input id="name" name="name" type="text" class="mt-2 block w-full rounded-xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Địa chỉ email" class="text-sm font-semibold text-slate-700" />
            <x-text-input id="email" name="email" type="email" class="mt-2 block w-full rounded-xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-3 rounded-xl bg-amber-50 p-3 text-sm leading-6 text-amber-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="ml-1 font-semibold underline underline-offset-2 hover:text-amber-950 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-semibold text-emerald-700">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="bio" value="Giới thiệu" class="text-sm font-semibold text-slate-700" />
            <textarea id="bio" name="bio" rows="4" class="mt-2 block w-full rounded-xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div class="flex flex-wrap items-center gap-4 border-t border-slate-100 pt-5">
            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">{{ __('Lưu thay đổi') }}</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-medium text-emerald-700"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
