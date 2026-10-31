<x-guest-layout title="Tạo tài khoản">
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-neutral-500">Bắt đầu ngay hôm nay</p>
        <h2 class="mt-2 text-2xl font-bold tracking-tight text-neutral-950">Tạo tài khoản</h2>
        <p class="mt-2 text-sm leading-6 text-neutral-500">Đăng ký miễn phí để bắt đầu hành trình học tập của bạn.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" value="Họ và tên" class="text-sm font-medium text-neutral-800" />
            <x-text-input id="name" class="mt-2 block w-full border-neutral-300 px-4 py-3 text-sm placeholder:text-neutral-400 focus:border-green-700 focus:ring-green-700/20" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nguyễn Văn An" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Địa chỉ email" class="text-sm font-medium text-neutral-800" />
            <x-text-input id="email" class="mt-2 block w-full border-neutral-300 px-4 py-3 text-sm placeholder:text-neutral-400 focus:border-green-700 focus:ring-green-700/20" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Nhập email của bạn" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Mật khẩu" class="text-sm font-medium text-neutral-800" />
            <x-text-input id="password" class="mt-2 block w-full border-neutral-300 px-4 py-3 text-sm placeholder:text-neutral-400 focus:border-green-700 focus:ring-green-700/20" type="password" name="password" required autocomplete="new-password" placeholder="Nhập mật khẩu" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Xác nhận mật khẩu" class="text-sm font-medium text-neutral-800" />
            <x-text-input id="password_confirmation" class="mt-2 block w-full border-neutral-300 px-4 py-3 text-sm placeholder:text-neutral-400 focus:border-green-700 focus:ring-green-700/20" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Nhập lại mật khẩu" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="mt-1 inline-flex w-full items-center justify-center rounded-md bg-green-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-700 focus:ring-offset-2">
            Tạo tài khoản
        </button>
    </form>

    <p class="mt-5 text-center text-xs leading-5 text-neutral-500">Tài khoản miễn phí · Bắt đầu học ngay sau khi đăng ký.</p>
</x-guest-layout>
