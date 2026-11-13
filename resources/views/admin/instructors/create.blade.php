@extends('layouts.admin')

@section('title', 'Tạo tài khoản giảng viên')
@section('page_title', 'Tạo Tài Khoản Giảng Viên')

@section('content')
<div class="max-w-2xl glass-card p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-gray-900 dark:text-neutral-100">Tạo tài khoản giảng viên mới</h2>
            <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">Chỉ quản trị viên mới có thể tạo và phân quyền tài khoản giảng viên.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200 flex items-center gap-1">
            <iconify-icon icon="lucide:arrow-left" class="text-sm"></iconify-icon>
            Quay lại
        </a>
    </div>

    <form method="POST" action="{{ route('admin.instructors.store') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200">Họ và tên <span class="text-red-500 dark:text-red-400">*</span></label>
            <input id="name" name="name" value="{{ old('name') }}" required autofocus
                   placeholder="Nguyễn Văn A"
                   class="mt-1.5 block w-full rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            @error('name') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200">Email <span class="text-red-500 dark:text-red-400">*</span></label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   placeholder="giangvien@elearning.vn"
                   class="mt-1.5 block w-full rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            @error('email') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200">Mật khẩu <span class="text-red-500 dark:text-red-400">*</span></label>
                <input id="password" type="password" name="password" required minlength="8"
                       placeholder="Tối thiểu 8 ký tự"
                       class="mt-1.5 block w-full rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
                @error('password') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200">Xác nhận mật khẩu <span class="text-red-500 dark:text-red-400">*</span></label>
                <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8"
                       placeholder="Nhập lại mật khẩu"
                       class="mt-1.5 block w-full rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            </div>
        </div>

        <div class="flex items-center gap-3 pt-3">
            <button type="submit" class="rounded-xl bg-[#019D91] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#008b80] shadow-sm transition-colors">
                Tạo tài khoản giảng viên
            </button>
            <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
