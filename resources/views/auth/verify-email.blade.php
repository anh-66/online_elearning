<x-guest-layout title="Xác thực email">
    <div class="mb-6">
        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-[#019D91]/10 text-[#019D91]">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg>
        </div>
        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-neutral-900 uppercase">
            XÁC THỰC EMAIL
        </h2>
        <p class="mt-2 text-sm text-neutral-500 leading-relaxed">
            Mã gồm 6 số đã được gửi tới <span class="font-semibold text-neutral-800 break-words">{{ $email }}</span>.
            Nhập mã để kích hoạt tài khoản. Mã có hiệu lực {{ config('otp.ttl_minutes', 10) }} phút.
        </p>
    </div>

    @php
        $notice = session('otp_notice', $notice ?? null);
        $problem = session('otp_problem', $problem ?? null);
    @endphp

    @if ($notice)
        <p class="mb-4 rounded-xl bg-[#019D91]/10 px-4 py-2.5 text-xs font-medium text-[#018076]" role="status">{{ $notice }}</p>
    @endif

    @if ($problem)
        <p class="mb-4 rounded-xl bg-amber-50 px-4 py-2.5 text-xs font-medium text-amber-800" role="alert">{{ $problem }}</p>
    @endif

    <form method="POST" action="{{ route('verification.verify') }}" class="space-y-4"
          x-data="{ sending: false }"
          x-on:otp-complete="if (! sending) { sending = true; $el.requestSubmit(); }"
          x-on:submit="sending = true">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-neutral-800 mb-2">Mã xác thực</label>
            <x-otp-input name="code" :autofocus="true" :invalid="$errors->has('code')" />
            <x-input-error :messages="$errors->get('code')" class="mt-2 text-xs" />
        </div>

        <button type="submit" x-bind:disabled="sending"
                class="w-full h-11 sm:h-12 mt-2 rounded-2xl bg-[#019D91] hover:bg-[#008b80] disabled:opacity-70 text-white text-sm sm:text-base font-semibold shadow-sm transition active:scale-[0.99] flex items-center justify-center">
            Xác thực email
        </button>
    </form>

    <div class="mt-5 flex flex-wrap items-center justify-between gap-2 rounded-xl border border-neutral-200 px-4 py-3">
        <p class="text-xs text-neutral-500">Chưa nhận được mã? Xem cả mục <span class="font-semibold text-neutral-700">Spam / Quảng cáo</span>.</p>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-otp-resend-button :seconds="$resendIn" />
        </form>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-5 text-center">
        @csrf
        <span class="text-xs sm:text-sm text-neutral-500">Nhập sai email?</span>
        <button type="submit" class="text-xs sm:text-sm font-semibold text-[#019D91] hover:underline transition">
            Đăng xuất và đăng ký lại
        </button>
    </form>
</x-guest-layout>
