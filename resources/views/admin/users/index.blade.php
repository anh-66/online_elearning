@extends('layouts.admin')

@section('title', 'Quản lý người dùng')
@section('page_title', 'Quản lý Người Dùng')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Người dùng hệ thống</h2>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-0.5">Quản lý tài khoản, phân quyền và trạng thái hoạt động.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.instructors.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#019D91] text-white text-sm font-semibold rounded-xl hover:bg-[#008b80] transition-colors shadow-sm">
                <iconify-icon icon="lucide:user-plus" class="text-lg"></iconify-icon>
                Tạo giảng viên
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="glass-card p-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="relative">
                <iconify-icon icon="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500 text-lg"></iconify-icon>
                <input type="text" name="keyword" value="{{ request('keyword') }}"
                       placeholder="Tên hoặc email..."
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition-all">
            </div>

            <select name="role"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] text-gray-600 dark:text-neutral-300 transition-all">
                <option value="">Tất cả vai trò</option>
                <option value="admin" @selected(request('role') === 'admin')>Quản trị viên</option>
                <option value="instructor" @selected(request('role') === 'instructor')>Giảng viên</option>
                <option value="student" @selected(request('role') === 'student')>Học viên</option>
            </select>

            <select name="status"
                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] text-gray-600 dark:text-neutral-300 transition-all">
                <option value="">Tất cả trạng thái</option>
                <option value="active" @selected(request('status') === 'active')>Hoạt động</option>
                <option value="blocked" @selected(request('status') === 'blocked')>Đã khóa</option>
            </select>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-[#019D91] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#008b80] transition-colors">
                    <iconify-icon icon="lucide:search" class="text-base"></iconify-icon>
                    Lọc
                </button>
                @if(request()->hasAny(['keyword', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}"
                       class="rounded-xl bg-gray-100 dark:bg-neutral-800 px-3.5 py-2.5 text-sm font-semibold text-gray-600 dark:text-neutral-300 hover:bg-gray-200 dark:hover:bg-neutral-700 transition-colors">
                        Đặt lại
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 dark:bg-neutral-800/30 text-gray-500 dark:text-neutral-400 text-xs font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Người dùng</th>
                        <th class="px-4 py-4">Vai trò</th>
                        <th class="px-4 py-4">Đổi vai trò</th>
                        <th class="px-4 py-4">Trạng thái</th>
                        <th class="px-4 py-4">Ngày tham gia</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-neutral-800/40 transition-colors">
                            {{-- Avatar + Name + Email --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center font-bold text-sm overflow-hidden">
                                            @if($user->avatar_url)
                                                <img src="{{ $user->avatar_url }}" referrerpolicy="no-referrer" alt="{{ $user->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')">
                                                <span class="hidden flex items-center justify-center w-full h-full">{{ $user->initialLetter() }}</span>
                                            @else
                                                <span>{{ $user->initialLetter() }}</span>
                                            @endif
                                        </div>
                                        @if($user->isActive())
                                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white dark:border-neutral-900 rounded-full"></span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-neutral-100 truncate">
                                            {{ $user->name }}
                                            @if($user->id === auth()->id())
                                                <span class="ml-1 text-xs font-normal text-[#019D91] dark:text-[#02b3a5]">(Bạn)</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-neutral-400 truncate">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Role Badge --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($user->role === 'admin')
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-[#019D91]"></span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-neutral-200">Admin</span>
                                    </div>
                                @elseif($user->role === 'instructor')
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-neutral-200">Giảng viên</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                        <span class="text-sm font-medium text-gray-700 dark:text-neutral-200">Học viên</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Role Switch --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($user->id === auth()->id())
                                    <span class="text-xs text-gray-400 dark:text-neutral-500 italic">Không thể đổi</span>
                                @else
                                    <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" onchange="this.form.submit()"
                                                class="text-xs rounded-lg border-gray-200 dark:border-neutral-800 py-1.5 pl-2 pr-6 bg-gray-50 dark:bg-neutral-800/40 focus:border-[#019D91] focus:ring-[#019D91]/20 cursor-pointer">
                                            <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                            <option value="instructor" @selected($user->role === 'instructor')>Giảng viên</option>
                                            <option value="student" @selected($user->role === 'student')>Học viên</option>
                                        </select>
                                    </form>
                                @endif
                            </td>

                            {{-- Status Toggle --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($user->id === auth()->id())
                                    <span class="status-badge bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">Hoạt động</span>
                                @else
                                    <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        @if($user->isActive())
                                            <button type="submit" title="Bấm để khóa tài khoản"
                                                    class="status-badge bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-colors cursor-pointer">
                                                Hoạt động
                                            </button>
                                        @else
                                            <button type="submit" title="Bấm để mở khóa"
                                                    class="status-badge bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors cursor-pointer">
                                                Đã khóa
                                            </button>
                                        @endif
                                    </form>
                                @endif
                            </td>

                            {{-- Created At --}}
                            <td class="px-4 py-4 text-sm text-gray-500 dark:text-neutral-400 whitespace-nowrap">
                                {{ $user->created_at?->format('d/m/Y') }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       title="Chỉnh sửa"
                                       class="p-2 text-gray-400 dark:text-neutral-500 hover:text-amber-600 dark:hover:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/10 rounded-lg transition-all">
                                        <iconify-icon icon="lucide:edit-3" class="text-lg"></iconify-icon>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                              data-confirm-message="Bạn có chắc chắn muốn xóa tài khoản {{ $user->name }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Xóa"
                                                    class="p-2 text-gray-400 dark:text-neutral-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-all">
                                                <iconify-icon icon="lucide:trash-2" class="text-lg"></iconify-icon>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <iconify-icon icon="lucide:users" class="text-gray-300 dark:text-neutral-600 text-4xl"></iconify-icon>
                                    <p class="text-sm text-gray-500 dark:text-neutral-400">Không tìm thấy người dùng phù hợp</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-neutral-800 bg-gray-50/30 dark:bg-neutral-800/20 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-gray-500 dark:text-neutral-400">
                    Hiển thị {{ $users->firstItem() }}–{{ $users->lastItem() }} trong tổng số {{ $users->total() }} người dùng
                </p>
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
