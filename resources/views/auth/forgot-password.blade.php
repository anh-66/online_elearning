<x-guest-layout title="Khôi phục mật khẩu">
    <x-password-reset-steps :current="1" />

    <div class="mb-6">
        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-neutral-900 uppercase">
            QUÊN MẬT KHẨU?
        </h2>
        <p class="mt-2 text-sm text-neutral-500 leading-relaxed">
            Đừng lo lắng! Hãy nhập địa chỉ email đã đăng ký, chúng tôi sẽ gửi mã gồm 6 số để bạn đặt lại mật khẩu mới.
        </p>
    </div>

    <x-auth-session-status class="mb-4 rounded-xl bg-[#019D91]/10 px-4 py-2.5 text-xs text-[#018076]" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-neutral-800 mb-1.5">Địa chỉ email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                   placeholder="name@example.com"
                   class="w-full h-11 px-3.5 text-base sm:text-sm rounded-xl border border-neutral-300 bg-white text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition shadow-sm">
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
        </div>

        <button type="submit" class="w-full h-11 sm:h-12 mt-2 rounded-2xl bg-[#019D91] hover:bg-[#008b80] text-white text-sm sm:text-base font-semibold shadow-sm transition active:scale-[0.99] flex items-center justify-center">
            Gửi mã xác nhận
        </button>

        <div class="text-center pt-3">
            <a href="{{ route('login') }}" class="text-xs sm:text-sm font-semibold text-[#019D91] hover:underline transition">
                &larr; Quay lại đăng nhập
            </a>
        </div>
    </form>
</x-guest-layout>
