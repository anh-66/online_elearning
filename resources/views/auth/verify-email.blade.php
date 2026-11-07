<x-guest-layout title="Xác minh email">
    <div class="mb-6">
        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-lg bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400">
            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M3 8l7.89 5.26a2 2 0 0 0 2.22 0L21 8M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg>
        </div>
        <h2 class="text-2xl font-bold tracking-tight text-neutral-950 dark:text-neutral-100">Xác minh địa chỉ email</h2>
        <p class="mt-2 text-sm leading-6 text-neutral-500 dark:text-neutral-400">Cảm ơn bạn đã đăng ký! Hãy bấm vào liên kết chúng tôi vừa gửi tới email của bạn để kích hoạt tài khoản. Nếu chưa nhận được, bạn có thể yêu cầu gửi lại.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <p class="mb-5 rounded-md bg-brand-50 dark:bg-brand-500/10 px-4 py-3 text-sm font-medium text-brand-800 dark:text-brand-300">
            Một liên kết xác minh mới đã được gửi tới email bạn dùng khi đăng ký.
        </p>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-brand-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-700 focus:ring-offset-2">
            Gửi lại email xác minh
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
        @csrf
        <button type="submit" class="text-sm font-medium text-neutral-600 dark:text-neutral-300 underline-offset-4 transition hover:text-brand-700 dark:hover:text-brand-300 hover:underline">
            Đăng xuất
        </button>
    </form>
</x-guest-layout>
