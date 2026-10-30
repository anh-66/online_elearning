<x-guest-layout title="Tạo tài khoản">
    <div class="mb-7">
        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-indigo-600">Bắt đầu ngay</p>
        <h2 class="mt-3 text-3xl font-semibold tracking-tight text-slate-900">{{ __('Tạo tài khoản') }}</h2>
        <p class="mt-2 text-sm leading-6 text-slate-500">Tham gia cộng đồng học tập và chọn cách bạn muốn phát triển.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" value="Họ và tên" class="text-sm font-semibold text-slate-700" />
            <x-text-input id="name" class="mt-2 block w-full rounded-md border-slate-300 px-4 py-3 text-sm shadow-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/15" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nguyễn Văn An" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Địa chỉ email" class="text-sm font-semibold text-slate-700" />
            <x-text-input id="email" class="mt-2 block w-full rounded-md border-slate-300 px-4 py-3 text-sm shadow-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/15" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="ban@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Mật khẩu" class="text-sm font-semibold text-slate-700" />
            <x-text-input id="password" class="mt-2 block w-full rounded-md border-slate-300 px-4 py-3 text-sm shadow-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/15" type="password" name="password" required autocomplete="new-password" placeholder="Tối thiểu 8 ký tự" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Xác nhận mật khẩu" class="text-sm font-semibold text-slate-700" />
            <x-text-input id="password_confirmation" class="mt-2 block w-full rounded-md border-slate-300 px-4 py-3 text-sm shadow-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/15" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Nhập lại mật khẩu" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label value="Bạn tham gia với vai trò" class="text-sm font-semibold text-slate-700" />

            <div class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <label for="role_student" class="flex cursor-pointer items-start gap-3 rounded-md border border-slate-200 p-4 transition hover:border-indigo-300 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/70 has-[:checked]:ring-1 has-[:checked]:ring-indigo-500/30">
                    <input id="role_student" type="radio" name="role" value="student" class="mt-1 border-slate-300 text-indigo-600 focus:ring-indigo-500" {{ old('role', 'student') === 'student' ? 'checked' : '' }} required>
                    <span>
                        <span class="block text-sm font-semibold text-slate-800">{{ __('Học viên') }}</span>
                        <span class="mt-1 block text-xs leading-5 text-slate-500">Học kỹ năng mới theo lộ trình của bạn.</span>
                    </span>
                </label>
                <label for="role_instructor" class="flex cursor-pointer items-start gap-3 rounded-md border border-slate-200 p-4 transition hover:border-indigo-300 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/70 has-[:checked]:ring-1 has-[:checked]:ring-indigo-500/30">
                    <input id="role_instructor" type="radio" name="role" value="instructor" class="mt-1 border-slate-300 text-indigo-600 focus:ring-indigo-500" {{ old('role') === 'instructor' ? 'checked' : '' }}>
                    <span>
                        <span class="block text-sm font-semibold text-slate-800">{{ __('Giảng viên') }}</span>
                        <span class="mt-1 block text-xs leading-5 text-slate-500">Chia sẻ kiến thức và xây dựng khóa học.</span>
                    </span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <button type="submit" class="mt-2 inline-flex w-full items-center justify-center gap-2 rounded-md bg-indigo-600 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            {{ __('Tạo tài khoản') }}
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-slate-500">
        {{ __('Đã có tài khoản?') }}
        <a href="{{ route('login') }}" class="font-semibold text-indigo-600 transition hover:text-indigo-700">{{ __('Đăng nhập') }}</a>
    </p>

    <p class="mt-4 text-center text-sm">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 font-semibold text-slate-500 transition hover:text-indigo-600">
            {{ __('Quay về trang chủ') }}
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path d="M5 12h14m-6-6 6 6-6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </a>
    </p>
</x-guest-layout>
