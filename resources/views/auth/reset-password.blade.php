<x-guest-layout title="Đặt lại mật khẩu">
    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-indigo-600">Bảo mật tài khoản</p>
        <h2 class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ __('Tạo mật khẩu mới') }}</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">Chọn mật khẩu mới để tiếp tục sử dụng tài khoản của bạn.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" value="Địa chỉ email" class="text-sm font-semibold text-slate-700" />
            <x-text-input id="email" class="mt-2 block w-full rounded-md border-slate-300 px-4 py-3 text-sm shadow-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/15" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="ban@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Mật khẩu mới" class="text-sm font-semibold text-slate-700" />
            <x-text-input id="password" class="mt-2 block w-full rounded-md border-slate-300 px-4 py-3 text-sm shadow-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/15" type="password" name="password" required autocomplete="new-password" placeholder="Nhập mật khẩu mới" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Xác nhận mật khẩu" class="text-sm font-semibold text-slate-700" />
            <x-text-input id="password_confirmation" class="mt-2 block w-full rounded-md border-slate-300 px-4 py-3 text-sm shadow-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/15" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Nhập lại mật khẩu mới" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            {{ __('Đặt lại mật khẩu') }}
        </button>
    </form>
</x-guest-layout>
