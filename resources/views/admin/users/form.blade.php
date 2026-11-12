@extends('layouts.admin')

@section('title', 'Sửa thông tin người dùng')
@section('page_title', 'Sửa Thông Tin Người Dùng')

@section('content')
<div class="max-w-2xl glass-card p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-gray-900 dark:text-neutral-100">Chỉnh sửa người dùng: {{ $user->name }}</h2>
            <p class="text-xs text-gray-500 dark:text-neutral-400 mt-0.5">Cập nhật thông tin cơ bản, vai trò và trạng thái tài khoản.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200 flex items-center gap-1">
            <iconify-icon icon="lucide:arrow-left" class="text-sm"></iconify-icon>
            Quay lại
        </a>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200">Họ và tên <span class="text-red-500 dark:text-red-400">*</span></label>
            <input id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus
                   class="mt-1.5 block w-full rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            @error('name') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200">Email <span class="text-red-500 dark:text-red-400">*</span></label>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="mt-1.5 block w-full rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            @error('email') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="bio" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200">Tiểu sử / Giới thiệu ngắn</label>
            <textarea id="bio" name="bio" rows="3" placeholder="Giới thiệu về chuyên môn, kinh nghiệm giảng dạy..."
                      class="mt-1.5 block w-full rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">{{ old('bio', $user->bio) }}</textarea>
            @error('bio') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="role" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200">Vai trò <span class="text-red-500 dark:text-red-400">*</span></label>
                <select id="role" name="role" {{ $user->id === auth()->id() ? 'disabled' : '' }}
                        class="mt-1.5 block w-full rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Quản trị viên (Admin)</option>
                    <option value="instructor" @selected(old('role', $user->role) === 'instructor')>Giảng viên (Instructor)</option>
                    <option value="student" @selected(old('role', $user->role) === 'student')>Học viên (Student)</option>
                </select>
                @if($user->id === auth()->id())
                    <input type="hidden" name="role" value="{{ $user->role }}">
                    <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">Bạn không thể tự đổi vai trò của chính mình.</p>
                @endif
                @error('role') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200">Trạng thái <span class="text-red-500 dark:text-red-400">*</span></label>
                <select id="status" name="status" {{ $user->id === auth()->id() ? 'disabled' : '' }}
                        class="mt-1.5 block w-full rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
                    <option value="active" @selected(old('status', $user->status) === 'active')>Hoạt động (Active)</option>
                    <option value="blocked" @selected(old('status', $user->status) === 'blocked')>Đã khóa (Blocked)</option>
                </select>
                @if($user->id === auth()->id())
                    <input type="hidden" name="status" value="{{ $user->status }}">
                    <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">Bạn không thể tự khóa tài khoản của chính mình.</p>
                @endif
                @error('status') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200">Mật khẩu mới (bỏ trống nếu không đổi)</label>
            <input id="password" type="password" name="password" minlength="8" placeholder="Tối thiểu 8 ký tự"
                   class="mt-1.5 block w-full rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            @error('password') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3 pt-3">
            <button type="submit" class="rounded-xl bg-[#019D91] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#008b80] shadow-sm transition-colors">
                Cập nhật thông tin
            </button>
            <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-200">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
