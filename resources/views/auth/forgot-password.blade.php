<x-guest-layout title="Khôi phục mật khẩu">
    <div class="mb-8">
        <h2 class="text-3xl font-bold tracking-tight text-[#1D1D1F] dark:text-white">Quên mật khẩu?</h2>
        <p class="mt-2 text-sm text-[#86868B]">Đừng lo lắng. Hãy nhập địa chỉ email của bạn, chúng tôi sẽ gửi liên kết để đặt lại mật khẩu mới.</p>
    </div>

    <x-auth-session-status class="mb-5 rounded-2xl bg-[#019D91]/10 px-4 py-3 text-sm text-[#018076] dark:text-[#02b3a5]" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-[#1D1D1F] dark:text-neutral-200 mb-2">Địa chỉ email</label>
            <x-text-input id="email" class="w-full h-12 px-4 text-sm rounded-2xl" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs" />
        </div>

        <button type="submit" class="btn-apple-primary w-full h-12 text-sm font-semibold rounded-full shadow-md shadow-[#019D91]/20 mt-2">
            Gửi liên kết đặt lại mật khẩu
        </button>

        <div class="text-center pt-2">
            <a href="{{ route('login') }}" class="text-sm font-medium text-[#019D91] dark:text-[#02b3a5] hover:underline">
                &larr; Quay lại trang đăng nhập
            </a>
        </div>
    </form>
</x-guest-layout>
