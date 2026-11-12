@extends('layouts.admin')

@section('title', 'Sửa thông tin người dùng')
@section('page_title', 'Sửa Thông Tin Người Dùng')

@section('content')
<div class="max-w-2xl glass-card p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-gray-900">Chỉnh sửa người dùng: {{ $user->name }}</h2>
            <p class="text-xs text-gray-500 mt-0.5">Cập nhật thông tin cơ bản, vai trò và trạng thái tài khoản.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <iconify-icon icon="lucide:arrow-left" class="text-sm"></iconify-icon>
            Quay lại
        </a>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700">Họ và tên <span class="text-red-500">*</span></label>
            <input id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus
                   class="mt-1.5 block w-full rounded-xl border-gray-200 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700">Email <span class="text-red-500">*</span></label>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="mt-1.5 block w-full rounded-xl border-gray-200 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="bio" class="block text-sm font-semibold text-gray-700">Tiểu sử / Giới thiệu ngắn</label>
            <textarea id="bio" name="bio" rows="3" placeholder="Giới thiệu về chuyên môn, kinh nghiệm giảng dạy..."
                      class="mt-1.5 block w-full rounded-xl border-gray-200 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">{{ old('bio', $user->bio) }}</textarea>
            @error('bio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @php
                $currentRole = old('role', $user->role ?? 'student');
                $roleLabels = [
                    'admin' => 'Quản trị viên (Admin)',
                    'instructor' => 'Giảng viên (Instructor)',
                    'student' => 'Học viên (Student)',
                ];
                $currentRoleLabel = $roleLabels[$currentRole] ?? 'Học viên (Student)';
                $isSelfRole = $user->id === auth()->id();
            @endphp
            <div class="relative"
                 x-data="{
                     open: false,
                     disabled: {{ $isSelfRole ? 'true' : 'false' }},
                     selectedValue: '{{ $currentRole }}',
                     selectedLabel: '{{ $currentRoleLabel }}',
                     options: [
                         { value: 'admin', label: 'Quản trị viên (Admin)' },
                         { value: 'instructor', label: 'Giảng viên (Instructor)' },
                         { value: 'student', label: 'Học viên (Student)' },
                     ],
                     select(val, label) {
                         if (this.disabled) return;
                         this.selectedValue = val;
                         this.selectedLabel = label;
                         this.open = false;
                     }
                 }">
                <label class="block text-sm font-semibold text-gray-700">Vai trò <span class="text-red-500">*</span></label>
                <input type="hidden" name="role" :value="selectedValue">

                <!-- Combobox Trigger Button -->
                <button type="button"
                        @click="if (!disabled) open = !open"
                        :aria-expanded="open"
                        :disabled="disabled"
                        data-slot="combobox-trigger"
                        class="mt-1.5 w-full h-11 px-3.5 rounded-xl border border-gray-200 bg-white text-gray-900 text-sm flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition cursor-pointer select-none group/combobox-trigger disabled:bg-gray-100 disabled:cursor-not-allowed">
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
                @if($isSelfRole)
                    <p class="mt-1 text-xs text-amber-600">Bạn không thể tự đổi vai trò của chính mình.</p>
                @endif
                @error('role') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            @php
                $currentStatus = old('status', $user->status ?? 'active');
                $statusLabels = [
                    'active' => 'Hoạt động (Active)',
                    'blocked' => 'Đã khóa (Blocked)',
                ];
                $currentStatusLabel = $statusLabels[$currentStatus] ?? 'Hoạt động (Active)';
                $isSelfStatus = $user->id === auth()->id();
            @endphp
            <div class="relative"
                 x-data="{
                     open: false,
                     disabled: {{ $isSelfStatus ? 'true' : 'false' }},
                     selectedValue: '{{ $currentStatus }}',
                     selectedLabel: '{{ $currentStatusLabel }}',
                     options: [
                         { value: 'active', label: 'Hoạt động (Active)' },
                         { value: 'blocked', label: 'Đã khóa (Blocked)' },
                     ],
                     select(val, label) {
                         if (this.disabled) return;
                         this.selectedValue = val;
                         this.selectedLabel = label;
                         this.open = false;
                     }
                 }">
                <label class="block text-sm font-semibold text-gray-700">Trạng thái <span class="text-red-500">*</span></label>
                <input type="hidden" name="status" :value="selectedValue">

                <!-- Combobox Trigger Button -->
                <button type="button"
                        @click="if (!disabled) open = !open"
                        :aria-expanded="open"
                        :disabled="disabled"
                        data-slot="combobox-trigger"
                        class="mt-1.5 w-full h-11 px-3.5 rounded-xl border border-gray-200 bg-white text-gray-900 text-sm flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition cursor-pointer select-none group/combobox-trigger disabled:bg-gray-100 disabled:cursor-not-allowed">
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
                @if($isSelfStatus)
                    <p class="mt-1 text-xs text-amber-600">Bạn không thể tự khóa tài khoản của chính mình.</p>
                @endif
                @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700">Mật khẩu mới (bỏ trống nếu không đổi)</label>
            <input id="password" type="password" name="password" minlength="8" placeholder="Tối thiểu 8 ký tự"
                   class="mt-1.5 block w-full rounded-xl border-gray-200 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3 pt-3">
            <button type="submit" class="rounded-xl bg-[#019D91] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#008b80] shadow-sm transition-colors">
                Cập nhật thông tin
            </button>
            <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700">
                Hủy
            </a>
        </div>
    </form>
</div>
@endsection
