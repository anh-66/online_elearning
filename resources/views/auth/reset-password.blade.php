<x-guest-layout title="Nhập mã xác nhận">
    <x-password-reset-steps :current="2" />

    <div class="mb-6">
        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-neutral-900 uppercase">
            NHẬP MÃ XÁC NHẬN
        </h2>
        <p class="mt-2 text-sm text-neutral-500 leading-relaxed">
            Nhập mã gồm 6 số vừa gửi tới <span class="font-semibold text-neutral-800 break-words">{{ $email }}</span>.
            Mã đúng thì bạn mới tạo được mật khẩu mới.
        </p>
    </div>

    <x-auth-session-status class="mb-4 rounded-xl bg-[#019D91]/10 px-4 py-2.5 text-xs text-[#018076]" :status="session('status')" />

    @if (session('otp_problem'))
        <p class="mb-4 rounded-xl bg-amber-50 px-4 py-2.5 text-xs font-medium text-amber-800" role="alert">{{ session('otp_problem') }}</p>
    @endif

    <x-otp-local-hint />

    <form method="POST" action="{{ route('password.verify-code') }}" class="space-y-4"
          x-data="{ sending: false }"
          x-on:otp-complete="if (! sending) { sending = true; $el.requestSubmit(); }"
          x-on:submit="sending = true">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-neutral-800 mb-2">Mã xác nhận</label>
            <x-otp-input name="code" :autofocus="true" :invalid="$errors->has('code')" />
            <x-input-error :messages="$errors->get('code')" class="mt-2 text-xs" />
        </div>

        <button type="submit" x-bind:disabled="sending"
                class="w-full h-11 sm:h-12 mt-2 rounded-2xl bg-[#019D91] hover:bg-[#008b80] disabled:opacity-70 text-white text-sm sm:text-base font-semibold shadow-sm transition active:scale-[0.99] flex items-center justify-center">
            Xác nhận mã
        </button>
    </form>

    <div class="mt-5 flex flex-wrap items-center justify-between gap-2 rounded-xl border border-neutral-200 px-4 py-3">
        <p class="text-xs text-neutral-500">Chưa nhận được mã? Xem cả mục <span class="font-semibold text-neutral-700">Spam / Quảng cáo</span>.</p>
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <x-otp-resend-button :seconds="$resendIn" :sends-left="$sendsLeft ?? null" />
        </form>
    </div>

    <div class="mt-5 flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-xs sm:text-sm">
        <a href="{{ route('password.request') }}" class="font-semibold text-[#019D91] hover:underline transition">Nhập email khác</a>
        <span class="text-neutral-300" aria-hidden="true">|</span>
        <a href="{{ route('login') }}" class="font-semibold text-[#019D91] hover:underline transition">Quay lại đăng nhập</a>
    </div>
</x-guest-layout>
