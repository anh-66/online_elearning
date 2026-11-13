<x-guest-layout title="Tạo mật khẩu mới">
    <x-password-reset-steps :current="3" />

    <div class="mb-6">
        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-neutral-900 uppercase">
            TẠO MẬT KHẨU MỚI
        </h2>
        <p class="mt-2 text-sm text-neutral-500 leading-relaxed">
            Mã chính xác. Chọn mật khẩu mới cho <span class="font-semibold text-neutral-800 break-words">{{ $email }}</span>
            trong vòng {{ config('otp.reset_window_minutes', 10) }} phút.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        {{-- Giúp trình quản lý mật khẩu biết mật khẩu mới thuộc tài khoản nào. --}}
        <input type="email" name="username" value="{{ $email }}" autocomplete="username" class="hidden" tabindex="-1" aria-hidden="true" readonly>

        <div>
            <label for="password" class="block text-sm font-semibold text-neutral-800 mb-1.5">Mật khẩu mới</label>
            <input id="password" type="password" name="password" required autofocus autocomplete="new-password"
                   placeholder="••••••••"
                   class="w-full h-11 px-3.5 text-base sm:text-sm rounded-xl border border-neutral-300 bg-white text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition shadow-sm">
            <p class="mt-1.5 text-[10px] text-neutral-400 font-medium tracking-wide uppercase leading-normal">
                MẬT KHẨU PHẢI CÓ TỐI THIỂU 8 KÝ TỰ BAO GỒM CHỮ SỐ VÀ CHỮ CÁI.
            </p>
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-neutral-800 mb-1.5">Xác nhận mật khẩu</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   placeholder="••••••••"
                   class="w-full h-11 px-3.5 text-base sm:text-sm rounded-xl border border-neutral-300 bg-white text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition shadow-sm">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
        </div>

        <button type="submit" class="w-full h-11 sm:h-12 mt-2 rounded-2xl bg-[#019D91] hover:bg-[#008b80] text-white text-sm sm:text-base font-semibold shadow-sm transition active:scale-[0.99] flex items-center justify-center">
            Đổi mật khẩu
        </button>
    </form>

    <div class="text-center pt-5">
        <a href="{{ route('login') }}" class="text-xs sm:text-sm font-semibold text-[#019D91] hover:underline transition">
            &larr; Quay lại đăng nhập
        </a>
    </div>
</x-guest-layout>
