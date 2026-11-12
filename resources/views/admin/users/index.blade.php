@extends('layouts.admin')

@section('title', 'Quản lý người dùng')
@section('page_title', 'Quản lý Người Dùng')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 uppercase tracking-wide">Người dùng hệ thống</h2>
            <p class="text-sm text-gray-500 mt-0.5">Quản lý tài khoản, phân quyền và trạng thái hoạt động.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.instructors.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#019D91] text-white text-sm font-semibold rounded-xl hover:bg-[#008b80] transition-colors shadow-sm">
                <iconify-icon icon="lucide:user-plus" class="text-lg"></iconify-icon>
                Tạo giảng viên
            </a>
        </div>
    </div>

    {{-- Filters Toolbar (Minimalist, gray inputs, separated by a thin horizontal line) --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pb-6 border-b border-gray-100">
        <div class="relative">
            <iconify-icon icon="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg"></iconify-icon>
            <input type="text" name="keyword" value="{{ request('keyword') }}"
                   placeholder="Tên hoặc email..."
                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition-all">
        </div>

            @php
                $roleLabels = [
                    '' => 'Tất cả vai trò',
                    'admin' => 'Quản trị viên',
                    'instructor' => 'Giảng viên',
                    'student' => 'Học viên',
                ];
                $currentRoleVal = (string) request('role', '');
                $currentRoleLabel = $roleLabels[$currentRoleVal] ?? 'Tất cả vai trò';
            @endphp
            <div class="relative"
                 x-data="{
                     open: false,
                     selectedValue: '{{ $currentRoleVal }}',
                     selectedLabel: '{{ addslashes($currentRoleLabel) }}',
                     options: [
                         { value: '', label: 'Tất cả vai trò' },
                         { value: 'admin', label: 'Quản trị viên' },
                         { value: 'instructor', label: 'Giảng viên' },
                         { value: 'student', label: 'Học viên' },
                     ],
                     select(val, label) {
                         this.selectedValue = val;
                         this.selectedLabel = label;
                         this.open = false;
                     }
                 }">
                <input type="hidden" name="role" :value="selectedValue">

                <!-- Combobox Trigger Button -->
                <button type="button"
                        @click="open = !open"
                        :aria-expanded="open"
                        data-slot="combobox-trigger"
                        class="w-full h-11 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition cursor-pointer select-none group/combobox-trigger">
                    <span x-text="selectedLabel" class="truncate font-medium text-gray-800"></span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-hover/combobox-trigger:text-gray-700 shrink-0 pointer-events-none"
                         :class="open ? 'rotate-180 text-gray-700' : ''"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                    </svg>
                </button>

                <!-- Combobox Content Popup -->
                <div x-show="open"
                     x-cloak
                     @click.outside="open = false"
                     @keydown.escape.window="open = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1 scale-98"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 -translate-y-1 scale-98"
                     data-slot="combobox-content"
                     class="absolute left-0 right-0 top-full mt-1.5 z-50 rounded-xl border border-gray-200 bg-white shadow-xl ring-1 ring-black/5 p-1 max-h-60 overflow-y-auto space-y-0.5 custom-scrollbar">
                    <template x-for="item in options" :key="item.value">
                        <button type="button"
                                @click="select(item.value, item.label)"
                                data-slot="combobox-item"
                                class="w-full relative flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer select-none text-left"
                                :class="selectedValue === item.value 
                                    ? 'bg-neutral-100 text-neutral-900 font-medium' 
                                    : 'text-neutral-700 hover:bg-neutral-50'">
                            <span x-text="item.label"></span>
                            <svg x-show="selectedValue === item.value"
                                 class="w-4 h-4 text-neutral-900 shrink-0 pointer-events-none"
                                 fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </template>
                </div>
            </div>

            @php
                $statusLabels = [
                    '' => 'Tất cả trạng thái',
                    'active' => 'Hoạt động',
                    'blocked' => 'Đã khóa',
                ];
                $currentStatusVal = (string) request('status', '');
                $currentStatusLabel = $statusLabels[$currentStatusVal] ?? 'Tất cả trạng thái';
            @endphp
            <div class="relative"
                 x-data="{
                     open: false,
                     selectedValue: '{{ $currentStatusVal }}',
                     selectedLabel: '{{ addslashes($currentStatusLabel) }}',
                     options: [
                         { value: '', label: 'Tất cả trạng thái' },
                         { value: 'active', label: 'Hoạt động' },
                         { value: 'blocked', label: 'Đã khóa' },
                     ],
                     select(val, label) {
                         this.selectedValue = val;
                         this.selectedLabel = label;
                         this.open = false;
                     }
                 }">
                <input type="hidden" name="status" :value="selectedValue">

                <!-- Combobox Trigger Button -->
                <button type="button"
                        @click="open = !open"
                        :aria-expanded="open"
                        data-slot="combobox-trigger"
                        class="w-full h-11 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition cursor-pointer select-none group/combobox-trigger">
                    <span x-text="selectedLabel" class="truncate font-medium text-gray-800"></span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 group-hover/combobox-trigger:text-gray-700 shrink-0 pointer-events-none"
                         :class="open ? 'rotate-180 text-gray-700' : ''"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                    </svg>
                </button>

                <!-- Combobox Content Popup -->
                <div x-show="open"
                     x-cloak
                     @click.outside="open = false"
                     @keydown.escape.window="open = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1 scale-98"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 -translate-y-1 scale-98"
                     data-slot="combobox-content"
                     class="absolute left-0 right-0 top-full mt-1.5 z-50 rounded-xl border border-gray-200 bg-white shadow-xl ring-1 ring-black/5 p-1 max-h-60 overflow-y-auto space-y-0.5 custom-scrollbar">
                    <template x-for="item in options" :key="item.value">
                        <button type="button"
                                @click="select(item.value, item.label)"
                                data-slot="combobox-item"
                                class="w-full relative flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer select-none text-left"
                                :class="selectedValue === item.value 
                                    ? 'bg-neutral-100 text-neutral-900 font-medium' 
                                    : 'text-neutral-700 hover:bg-neutral-50'">
                            <span x-text="item.label"></span>
                            <svg x-show="selectedValue === item.value"
                                 class="w-4 h-4 text-neutral-900 shrink-0 pointer-events-none"
                                 fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </template>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-[#019D91] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#008b80] transition-colors">
                    <iconify-icon icon="lucide:search" class="text-base"></iconify-icon>
                    Lọc
                </button>
                @if(request()->hasAny(['keyword', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}"
                       class="rounded-xl bg-gray-100 px-3.5 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-200 transition-colors">
                        Đặt lại
                    </a>
                @endif
            </div>
    </form>

    {{-- Users Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="border-b border-gray-100 text-gray-400 text-xs font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Người dùng</th>
                        <th class="px-4 py-4">Vai trò</th>
                        <th class="px-4 py-4">Đổi vai trò</th>
                        <th class="px-4 py-4">Trạng thái</th>
                        <th class="px-4 py-4">Ngày tham gia</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            {{-- Avatar + Name + Email --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center font-bold text-sm overflow-hidden">
                                            @if($user->avatar_url)
                                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')">
                                                <span class="hidden flex items-center justify-center w-full h-full">{{ $user->initialLetter() }}</span>
                                            @else
                                                <span>{{ $user->initialLetter() }}</span>
                                            @endif
                                        </div>
                                        @if($user->isActive())
                                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">
                                            {{ $user->name }}
                                            @if($user->id === auth()->id())
                                                <span class="ml-1 text-xs font-normal text-[#019D91]">(Bạn)</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Role Badge --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($user->role === 'admin')
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-[#019D91]"></span>
                                        <span class="text-sm font-medium text-gray-700">Admin</span>
                                    </div>
                                @elseif($user->role === 'instructor')
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                        <span class="text-sm font-medium text-gray-700">Giảng viên</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                        <span class="text-sm font-medium text-gray-700">Học viên</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Role Switch --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($user->id === auth()->id())
                                    <span class="text-xs text-gray-400 italic">Không thể đổi</span>
                                @else
                                    <form method="POST" action="{{ route('admin.users.update-role', $user) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <div class="relative inline-block"
                                             x-data="{
                                                 open: false,
                                                 selectedValue: '{{ $user->role }}',
                                                 selectedLabel: '{{ $user->role === 'admin' ? 'Admin' : ($user->role === 'instructor' ? 'Giảng viên' : 'Học viên') }}',
                                                 options: [
                                                     { value: 'admin', label: 'Admin' },
                                                     { value: 'instructor', label: 'Giảng viên' },
                                                     { value: 'student', label: 'Học viên' }
                                                 ],
                                                 select(val, label) {
                                                     if (this.selectedValue === val) { this.open = false; return; }
                                                     this.selectedValue = val;
                                                     this.selectedLabel = label;
                                                     this.open = false;
                                                     this.$nextTick(() => { this.$el.closest('form').submit(); });
                                                 }
                                             }">
                                            <input type="hidden" name="role" :value="selectedValue">
                                            <button type="button" @click="open = !open"
                                                    class="text-xs rounded-lg border border-gray-200 py-1.5 pl-2.5 pr-2 bg-gray-50 hover:bg-gray-100 flex items-center gap-1.5 focus:border-[#019D91] focus:ring-1 focus:ring-[#019D91]/20 cursor-pointer">
                                                <span x-text="selectedLabel" class="font-medium text-gray-800"></span>
                                                <iconify-icon icon="lucide:chevron-down" class="text-xs text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"></iconify-icon>
                                            </button>
                                            <div x-show="open" x-cloak @click.outside="open = false"
                                                 x-transition:enter="transition ease-out duration-100"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 class="absolute left-0 mt-1 z-50 rounded-xl border border-gray-200 bg-white shadow-xl p-1 min-w-[125px] text-xs space-y-0.5">
                                                <template x-for="item in options" :key="item.value">
                                                    <button type="button" @click="select(item.value, item.label)"
                                                            class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg hover:bg-neutral-50 text-left transition-colors cursor-pointer"
                                                            :class="selectedValue === item.value ? 'bg-neutral-100 font-semibold text-neutral-900' : 'text-neutral-700'">
                                                        <span x-text="item.label"></span>
                                                        <iconify-icon icon="lucide:check" x-show="selectedValue === item.value" class="text-xs text-neutral-900"></iconify-icon>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            </td>

                            {{-- Status Toggle --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($user->id === auth()->id())
                                    <span class="status-badge bg-emerald-50 text-emerald-600">Hoạt động</span>
                                @else
                                    <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        @if($user->isActive())
                                            <button type="submit" title="Bấm để khóa tài khoản"
                                                    class="status-badge bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors cursor-pointer">
                                                Hoạt động
                                            </button>
                                        @else
                                            <button type="submit" title="Bấm để mở khóa"
                                                    class="status-badge bg-red-50 text-red-600 hover:bg-red-100 transition-colors cursor-pointer">
                                                Đã khóa
                                            </button>
                                        @endif
                                    </form>
                                @endif
                            </td>

                            {{-- Created At --}}
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $user->created_at?->format('d/m/Y') }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       title="Chỉnh sửa"
                                       class="text-gray-400 hover:text-gray-900 transition-colors">
                                        <iconify-icon icon="lucide:edit-3" class="text-base"></iconify-icon>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                              data-confirm-message="Bạn có chắc chắn muốn xóa tài khoản {{ $user->name }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Xóa"
                                                    class="text-gray-400 hover:text-red-600 transition-colors flex items-center">
                                                <iconify-icon icon="lucide:trash-2" class="text-base"></iconify-icon>
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
                                    <iconify-icon icon="lucide:users" class="text-gray-300 text-4xl"></iconify-icon>
                                    <p class="text-sm text-gray-500">Không tìm thấy người dùng phù hợp</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-gray-500">
                    Hiển thị {{ $users->firstItem() }}–{{ $users->lastItem() }} trong tổng số {{ $users->total() }} người dùng
                </p>
                {{ $users->links() }}
            </div>
        @endif
</div>
@endsection
