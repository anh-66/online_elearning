<x-guest-layout title="Tạo tài khoản">
    <div class="mb-6">
        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-neutral-900 uppercase">
            TẠO TÀI KHOẢN MỚI
        </h2>
    </div>

    {{-- Google OAuth Button --}}
    <a href="{{ route('auth.google') }}" class="w-full h-12 flex items-center justify-center gap-3 rounded-2xl border border-neutral-300 bg-white px-4 text-sm font-semibold text-neutral-800 hover:bg-neutral-50 shadow-sm transition active:scale-[0.99]">
        <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
            <path fill="#4285F4" d="M21.35 12.23c0-.72-.06-1.42-.18-2.09H12v3.96h5.24a4.48 4.48 0 0 1-1.94 2.94v2.45h3.14c1.84-1.7 2.91-4.2 2.91-7.26Z" />
            <path fill="#34A853" d="M12 21.67c2.63 0 4.84-.87 6.45-2.36l-3.14-2.45c-.87.58-1.98.93-3.31.93-2.54 0-4.7-1.72-5.47-4.03H3.28v2.53A9.74 9.74 0 0 0 12 21.67Z" />
            <path fill="#FBBC05" d="M6.53 13.76a5.86 5.86 0 0 1 0-3.52V7.71H3.28a9.75 9.75 0 0 0 0 8.58l3.25-2.53Z" />
            <path fill="#EA4335" d="M12 6.21c1.43 0 2.72.49 3.74 1.46l2.8-2.8C16.84 3.3 14.63 2.33 12 2.33a9.74 9.74 0 0 0-8.72 5.38l3.25 2.53C7.3 7.93 9.46 6.21 12 6.21Z" />
        </svg>
        <span>Đăng ký với Google</span>
    </a>

    {{-- Divider --}}
    <div class="my-6 flex items-center gap-3">
        <span class="h-px flex-1 bg-neutral-300"></span>
        <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider">HOẶC</span>
        <span class="h-px flex-1 bg-neutral-300"></span>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Name --}}
        <div>
            <label for="name" class="block text-sm font-semibold text-neutral-800 mb-1.5">Họ và tên</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   placeholder="Nguyễn Văn An"
                   class="w-full h-11 px-3.5 text-sm rounded-xl border border-neutral-300 bg-white text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition">
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-semibold text-neutral-800 mb-1.5">Địa chỉ email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                   placeholder="name@example.com"
                   class="w-full h-11 px-3.5 text-sm rounded-xl border border-neutral-300 bg-white text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition">
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-semibold text-neutral-800 mb-1.5">Mật khẩu</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   placeholder="••••••••"
                   class="w-full h-11 px-3.5 text-sm rounded-xl border border-neutral-300 bg-white text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition">
            <p class="mt-1.5 text-[10px] text-neutral-400 font-medium tracking-wide uppercase leading-normal">
                MẬT KHẨU PHẢI CÓ TỐI THIỂU 8 KÝ TỰ BAO GỒM CHỮ SỐ VÀ CHỮ CÁI.
            </p>
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-neutral-800 mb-1.5">Xác nhận mật khẩu</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   placeholder="••••••••"
                   class="w-full h-11 px-3.5 text-sm rounded-xl border border-neutral-300 bg-white text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
        </div>

        {{-- Submit Button (Teal #019D91, Rounded-2xl Ratio) --}}
        <button type="submit" class="w-full h-12 mt-4 rounded-2xl bg-[#019D91] hover:bg-[#008b80] text-white text-base font-semibold shadow-sm transition active:scale-[0.99] flex items-center justify-center">
            Đăng ký tài khoản
        </button>

        {{-- Legal Disclaimer --}}
        <p class="pt-3 text-center text-xs text-neutral-700 leading-relaxed font-normal">
            Bằng cách tạo tài khoản, bạn đồng ý với <a href="#" class="text-red-600 font-semibold hover:underline">Điều khoản Dịch vụ</a>.<br>
            Để biết thêm thông tin về cách bảo mật của chúng tôi, hãy xem <a href="#" class="text-red-600 font-semibold hover:underline">Chính sách Bảo mật</a>.
        </p>
    </form>
</x-guest-layout>
