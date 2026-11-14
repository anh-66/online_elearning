@extends('layouts.public')

@section('title', 'Cài đặt người dùng')

@push('styles')
<style>
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
        animation: fadeIn 0.25s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endpush

@push('scripts')
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
@endpush

@section('content')
<div class="min-h-[calc(100vh-140px)] bg-white text-gray-900 dark:bg-[#0d1117] dark:text-gray-100 border-b border-gray-100 dark:border-neutral-800"
     x-init="
         const handleHash = () => {
             let h = window.location.hash.replace('#', '');
             let allowed = @js($user->isStudent() ? ['account', 'achievements', 'notifications', 'privacy', 'display', 'billing'] : ['account', 'achievements', 'notifications', 'privacy', 'display']);
             if (h === 'password') {
                 activeTab = 'privacy';
                 setTimeout(() => document.getElementById('password')?.scrollIntoView({ behavior: 'smooth' }), 100);
             } else if (allowed.includes(h)) {
                 activeTab = h;
             }
         };
         handleHash();
         window.addEventListener('hashchange', handleHash);
         window.addEventListener('theme-changed', (e) => {
             if (e.detail?.theme) {
                 currentTheme = e.detail.theme;
             }
         });
         window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
             if (currentTheme === 'system') {
                 document.documentElement.classList.toggle('dark', e.matches);
                 window.dispatchEvent(new CustomEvent('theme-changed', {
                     detail: { theme: 'system', dark: e.matches }
                 }));
             }
         });
     "
     x-data="{
         activeTab: (function() {
             let h = window.location.hash.replace('#', '');
             if (h === 'password') return 'privacy';
             let allowed = @js($user->isStudent() ? ['account', 'achievements', 'notifications', 'privacy', 'display', 'billing'] : ['account', 'achievements', 'notifications', 'privacy', 'display']);
             if (allowed.includes(h)) return h;
             return @js($errors->updatePassword->isNotEmpty() ? 'privacy' : ($errors->userDeletion->isNotEmpty() ? 'privacy' : 'account'));
         })(),
         tabTitles: {
             'account': 'Cài đặt tài khoản',
             'achievements': 'Thành tựu & Huy hiệu',
             'notifications': 'Cài đặt thông báo',
             'privacy': 'Quyền riêng tư & Bảo mật',
             'display': 'Giao diện & Hiển thị',
             'billing': 'Đăng ký & Thanh toán'
         },
         tabDescriptions: {
             'account': 'Quản lý thông tin cá nhân và cấu hình tài khoản của bạn.',
             'achievements': 'Theo dõi tiến trình học tập, mở khóa huy hiệu và bảng thành tích cá nhân.',
             'notifications': 'Tùy chỉnh cách bạn nhận tin tức và cập nhật từ hệ thống.',
             'privacy': 'Quản lý bảo mật tài khoản và dữ liệu cá nhân của bạn.',
             'display': 'Tùy chỉnh cách ứng dụng hiển thị trên thiết bị của bạn.',
             'billing': 'Quản lý các khóa học đã ghi danh và lịch sử thanh toán.'
         },
         // Notifications toggles
         emailActivity: true,
         emailNewsletter: false,
         pushDiscussion: true,
         pushReminder: false,
         // Theme selection - mặc định là 'light' khi vào web
         currentTheme: localStorage.getItem('theme') || 'light',
         setTheme(mode) {
             this.currentTheme = mode;
             let isDark = false;
             if (mode === 'dark') {
                 isDark = true;
                 document.documentElement.classList.add('dark');
                 localStorage.setItem('theme', 'dark');
             } else if (mode === 'light') {
                 isDark = false;
                 document.documentElement.classList.remove('dark');
                 localStorage.setItem('theme', 'light');
             } else {
                 isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                 document.documentElement.classList.toggle('dark', isDark);
                 localStorage.setItem('theme', 'system');
             }
             window.dispatchEvent(new CustomEvent('theme-changed', {
                 detail: { theme: mode, dark: isDark }
             }));
         }
     }">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 sm:py-12 flex flex-col md:flex-row gap-8 lg:gap-12">
        <!-- Sidebar Navigation -->
        <aside class="w-full md:w-64 shrink-0 flex flex-col">
            <nav class="flex-1 space-y-1">
                <button type="button" @click="activeTab = 'account'"
                        :class="activeTab === 'account' ? 'bg-gray-100 dark:bg-neutral-800 font-medium text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-neutral-800/50 hover:text-gray-900 dark:hover:text-white'"
                        class="w-full group flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg transition-all text-left">
                    <iconify-icon icon="lucide:user" class="text-lg"></iconify-icon>
                    <span>Tài khoản</span>
                </button>

                <button type="button" @click="activeTab = 'achievements'"
                        :class="activeTab === 'achievements' ? 'bg-gray-100 dark:bg-neutral-800 font-medium text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-neutral-800/50 hover:text-gray-900 dark:hover:text-white'"
                        class="w-full group flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg transition-all text-left">
                    <iconify-icon icon="lucide:trophy" class="text-lg text-amber-500"></iconify-icon>
                    <span>Thành tựu</span>
                </button>

                <button type="button" @click="activeTab = 'notifications'"
                        :class="activeTab === 'notifications' ? 'bg-gray-100 dark:bg-neutral-800 font-medium text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-neutral-800/50 hover:text-gray-900 dark:hover:text-white'"
                        class="w-full group flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg transition-all text-left">
                    <iconify-icon icon="lucide:bell" class="text-lg"></iconify-icon>
                    <span>Thông báo</span>
                </button>

                <button type="button" @click="activeTab = 'privacy'"
                        :class="activeTab === 'privacy' ? 'bg-gray-100 dark:bg-neutral-800 font-medium text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-neutral-800/50 hover:text-gray-900 dark:hover:text-white'"
                        class="w-full group flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg transition-all text-left">
                    <iconify-icon icon="lucide:shield" class="text-lg"></iconify-icon>
                    <span>Quyền riêng tư</span>
                </button>

                <button type="button" @click="activeTab = 'display'"
                        :class="activeTab === 'display' ? 'bg-gray-100 dark:bg-neutral-800 font-medium text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-neutral-800/50 hover:text-gray-900 dark:hover:text-white'"
                        class="w-full group flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg transition-all text-left">
                    <iconify-icon icon="lucide:monitor" class="text-lg"></iconify-icon>
                    <span>Hiển thị</span>
                </button>

                @if($user->isStudent())
                    <button type="button" @click="activeTab = 'billing'"
                            :class="activeTab === 'billing' ? 'bg-gray-100 dark:bg-neutral-800 font-medium text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-neutral-800/50 hover:text-gray-900 dark:hover:text-white'"
                            class="w-full group flex items-center gap-3 px-4 py-2.5 text-sm rounded-lg transition-all text-left">
                        <iconify-icon icon="lucide:credit-card" class="text-lg"></iconify-icon>
                        <span>Thanh toán</span>
                    </button>
                @endif
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 max-w-3xl min-w-0">
            <!-- Header -->
            <header class="mb-10">
                <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight mb-2 text-gray-900 dark:text-white" x-text="tabTitles[activeTab]">Cài đặt tài khoản</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="tabDescriptions[activeTab]">Quản lý thông tin cá nhân và cấu hình tài khoản của bạn.</p>
            </header>

            <!-- TAB 1: TÀI KHOẢN -->
            <section x-show="activeTab === 'account'" class="tab-content active space-y-10">
                @include('profile.partials.update-profile-information-form')
            </section>

            <!-- TAB 2: THÀNH TỰU (ACHIEVEMENTS) -->
            <section x-show="activeTab === 'achievements'" x-cloak class="tab-content active space-y-10">
                @include('profile.partials.achievements')
            </section>

            <!-- TAB 3: THÔNG BÁO -->
            <section x-show="activeTab === 'notifications'" x-cloak class="tab-content active space-y-10">
                <div class="space-y-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Email thông báo</h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Hoạt động tài khoản</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Nhận email khi có hoạt động đăng nhập từ thiết bị mới hoặc đổi mật khẩu.</p>
                            </div>
                            <button type="button" @click="emailActivity = !emailActivity"
                                    :class="emailActivity ? 'bg-[#0071e3] dark:bg-[#0071e3]' : 'bg-gray-200 dark:bg-neutral-700'"
                                    class="w-11 h-6 rounded-full relative transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                <div :class="emailActivity ? 'translate-x-5' : 'translate-x-0'"
                                     class="absolute left-1 top-1 w-4 h-4 rounded-full bg-white shadow-sm transition-transform"></div>
                            </button>
                        </div>

                        <div class="flex items-center justify-between py-2 border-t border-gray-50 dark:border-neutral-800">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Bản tin & Khóa học mới</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Cập nhật các tính năng, công nghệ mới và khóa học được đề xuất.</p>
                            </div>
                            <button type="button" @click="emailNewsletter = !emailNewsletter"
                                    :class="emailNewsletter ? 'bg-[#0071e3] dark:bg-[#0071e3]' : 'bg-gray-200 dark:bg-neutral-700'"
                                    class="w-11 h-6 rounded-full relative transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                <div :class="emailNewsletter ? 'translate-x-5' : 'translate-x-0'"
                                     class="absolute left-1 top-1 w-4 h-4 rounded-full bg-white shadow-sm transition-transform"></div>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 pt-6 border-t border-gray-100 dark:border-neutral-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Thông báo đẩy</h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Thảo luận bài học</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Nhận thông báo khi có người trả lời câu hỏi hoặc phản hồi bình luận của bạn.</p>
                            </div>
                            <button type="button" @click="pushDiscussion = !pushDiscussion"
                                    :class="pushDiscussion ? 'bg-[#0071e3] dark:bg-[#0071e3]' : 'bg-gray-200 dark:bg-neutral-700'"
                                    class="w-11 h-6 rounded-full relative transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                <div :class="pushDiscussion ? 'translate-x-5' : 'translate-x-0'"
                                     class="absolute left-1 top-1 w-4 h-4 rounded-full bg-white shadow-sm transition-transform"></div>
                            </button>
                        </div>

                        <div class="flex items-center justify-between py-2 border-t border-gray-50 dark:border-neutral-800">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Nhắc nhở học tập</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Nhắc nhở tiếp tục tiến độ bài học dang dở vào các ngày cuối tuần.</p>
                            </div>
                            <button type="button" @click="pushReminder = !pushReminder"
                                    :class="pushReminder ? 'bg-[#0071e3] dark:bg-[#0071e3]' : 'bg-gray-200 dark:bg-neutral-700'"
                                    class="w-11 h-6 rounded-full relative transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                <div :class="pushReminder ? 'translate-x-5' : 'translate-x-0'"
                                     class="absolute left-1 top-1 w-4 h-4 rounded-full bg-white shadow-sm transition-transform"></div>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- TAB 3: QUYỀN RIÊNG TƯ -->
            <section x-show="activeTab === 'privacy'" x-cloak class="tab-content active space-y-10">
                @include('profile.partials.update-password-form')

                <div class="space-y-4 pt-6 border-t border-gray-100 dark:border-neutral-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Dữ liệu cá nhân</h2>
                    <div class="p-4 bg-gray-50 dark:bg-[#161b22] rounded-xl border border-gray-100 dark:border-neutral-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <iconify-icon icon="lucide:download" class="text-xl text-gray-500 shrink-0"></iconify-icon>
                            <div class="min-w-0">
                                <span class="text-sm font-medium text-gray-900 dark:text-white block">Tải xuống dữ liệu của tôi</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Xuất bản sao hồ sơ cá nhân, khóa học và lịch sử học tập (file .JSON).</span>
                            </div>
                        </div>
                        <a href="{{ route('profile.export-data') }}"
                           class="text-xs font-semibold px-4 py-2 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-lg hover:bg-gray-50 dark:hover:bg-neutral-700 transition shrink-0 inline-flex items-center justify-center gap-1.5 shadow-sm">
                            <iconify-icon icon="lucide:download" class="text-sm"></iconify-icon>
                            Tải về file
                        </a>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 dark:border-neutral-800">
                    @include('profile.partials.delete-user-form')
                </div>
            </section>

            <!-- TAB 4: HIỂN THỊ -->
            <section x-show="activeTab === 'display'" x-cloak class="tab-content active space-y-10">
                <div class="space-y-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Giao diện hệ thống</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <button type="button" @click="setTheme('light')"
                                :class="currentTheme === 'light' ? 'border-black dark:border-white bg-gray-50 dark:bg-neutral-800 shadow-sm' : 'border-gray-200 dark:border-neutral-700 hover:border-gray-300'"
                                class="p-4 border rounded-xl text-left transition-all">
                            <div class="w-full h-20 bg-white border border-gray-100 rounded mb-3 flex items-center justify-center">
                                <div class="w-12 h-2 bg-gray-200 rounded-full"></div>
                            </div>
                            <span :class="currentTheme === 'light' ? 'font-semibold text-gray-900 dark:text-white' : 'font-medium text-gray-500 dark:text-gray-400'" class="text-xs">Sáng</span>
                        </button>

                        <button type="button" @click="setTheme('dark')"
                                :class="currentTheme === 'dark' ? 'border-black dark:border-white bg-gray-50 dark:bg-neutral-800 shadow-sm' : 'border-gray-200 dark:border-neutral-700 hover:border-gray-300'"
                                class="p-4 border rounded-xl text-left transition-all">
                            <div class="w-full h-20 bg-gray-900 rounded mb-3 flex items-center justify-center">
                                <div class="w-12 h-2 bg-gray-700 rounded-full"></div>
                            </div>
                            <span :class="currentTheme === 'dark' ? 'font-semibold text-gray-900 dark:text-white' : 'font-medium text-gray-500 dark:text-gray-400'" class="text-xs">Tối</span>
                        </button>

                        <button type="button" @click="setTheme('system')"
                                :class="currentTheme === 'system' ? 'border-black dark:border-white bg-gray-50 dark:bg-neutral-800 shadow-sm' : 'border-gray-200 dark:border-neutral-700 hover:border-gray-300'"
                                class="p-4 border rounded-xl text-left transition-all">
                            <div class="w-full h-20 bg-gray-100 dark:bg-neutral-900 rounded mb-3 flex items-center justify-center gap-1">
                                <div class="w-6 h-12 bg-white rounded-sm border border-gray-200"></div>
                                <div class="w-6 h-12 bg-gray-900 rounded-sm"></div>
                            </div>
                            <span :class="currentTheme === 'system' ? 'font-semibold text-gray-900 dark:text-white' : 'font-medium text-gray-500 dark:text-gray-400'" class="text-xs">Hệ thống</span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-6 border-t border-gray-100 dark:border-neutral-800">
                    <div class="space-y-2 relative"
                         x-data="{
                             open: false,
                             selectedValue: 'vi',
                             selectedLabel: 'Tiếng Việt',
                             options: [
                                 { value: 'vi', label: 'Tiếng Việt' },
                                 { value: 'en', label: 'English' },
                                 { value: 'ja', label: '日本語' }
                             ],
                             select(val, label) {
                                 this.selectedValue = val;
                                 this.selectedLabel = label;
                                 this.open = false;
                             }
                         }">
                        <label class="text-sm font-medium text-gray-900 dark:text-white">Ngôn ngữ</label>
                        <button type="button"
                                @click="open = !open"
                                :aria-expanded="open"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] rounded-lg text-sm text-gray-900 dark:text-white flex items-center justify-between text-left focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 transition-all cursor-pointer select-none">
                            <span x-text="selectedLabel" class="truncate font-medium"></span>
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200"
                                 :class="open ? 'rotate-180 text-gray-700 dark:text-gray-300' : ''"
                                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-cloak
                             @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1 scale-98"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 -translate-y-1 scale-98"
                             class="absolute left-0 right-0 top-full mt-1.5 z-50 rounded-xl border border-gray-200 dark:border-[#30363d] bg-white dark:bg-[#161b22] shadow-xl ring-1 ring-black/5 dark:ring-white/5 p-1 overflow-hidden space-y-0.5">
                            <template x-for="item in options" :key="item.value">
                                <button type="button"
                                        @click="select(item.value, item.label)"
                                        class="w-full relative flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer select-none text-left"
                                        :class="selectedValue === item.value 
                                            ? 'bg-neutral-100 dark:bg-neutral-800 text-neutral-900 dark:text-white font-medium' 
                                            : 'text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-800/60'">
                                    <span x-text="item.label"></span>
                                    <svg x-show="selectedValue === item.value"
                                         class="w-4 h-4 text-neutral-900 dark:text-white shrink-0 pointer-events-none transition-transform duration-200"
                                         fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="space-y-2 relative"
                         x-data="{
                             open: false,
                             selectedValue: 'normal',
                             selectedLabel: 'Mặc định',
                             options: [
                                 { value: 'normal', label: 'Mặc định' },
                                 { value: 'small', label: 'Nhỏ' },
                                 { value: 'large', label: 'Lớn' }
                             ],
                             select(val, label) {
                                 this.selectedValue = val;
                                 this.selectedLabel = label;
                                 this.open = false;
                             }
                         }">
                        <label class="text-sm font-medium text-gray-900 dark:text-white">Cỡ chữ</label>
                        <button type="button"
                                @click="open = !open"
                                :aria-expanded="open"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] rounded-lg text-sm text-gray-900 dark:text-white flex items-center justify-between text-left focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 transition-all cursor-pointer select-none">
                            <span x-text="selectedLabel" class="truncate font-medium"></span>
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200"
                                 :class="open ? 'rotate-180 text-gray-700 dark:text-gray-300' : ''"
                                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open"
                             x-cloak
                             @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1 scale-98"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 -translate-y-1 scale-98"
                             class="absolute left-0 right-0 top-full mt-1.5 z-50 rounded-xl border border-gray-200 dark:border-[#30363d] bg-white dark:bg-[#161b22] shadow-xl ring-1 ring-black/5 dark:ring-white/5 p-1 overflow-hidden space-y-0.5">
                            <template x-for="item in options" :key="item.value">
                                <button type="button"
                                        @click="select(item.value, item.label)"
                                        class="w-full relative flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer select-none text-left"
                                        :class="selectedValue === item.value 
                                            ? 'bg-neutral-100 dark:bg-neutral-800 text-neutral-900 dark:text-white font-medium' 
                                            : 'text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-800/60'">
                                    <span x-text="item.label"></span>
                                    <svg x-show="selectedValue === item.value"
                                         class="w-4 h-4 text-neutral-900 dark:text-white shrink-0 pointer-events-none transition-transform duration-200"
                                         fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </section>

            @if($user->isStudent())
            <!-- TAB 5: THANH TOÁN -->
            <section x-show="activeTab === 'billing'" x-cloak class="tab-content active space-y-10">
                <div class="p-6 border border-gray-100 dark:border-neutral-800 rounded-2xl bg-gray-50 dark:bg-[#161b22] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Tài khoản</span>
                        <h3 class="text-xl font-bold mt-1 text-gray-900 dark:text-white">
                            {{ $user->isAdmin() ? 'Quản trị viên' : ($user->isInstructor() ? 'Giảng viên' : 'Học viên EduLearn') }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Đã tham gia hệ thống từ {{ $user->created_at?->format('d/m/Y') ?? '2026' }}
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        @if (Route::has('my-courses'))
                            <a href="{{ route('my-courses') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-black text-white dark:bg-white dark:text-black text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-200 transition-colors">
                                Khóa học của tôi
                            </a>
                        @endif
                        <button type="button"
                                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                                class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-950/40 border border-red-200 dark:border-red-900/60 rounded-lg transition-colors">
                            <iconify-icon icon="lucide:trash-2" class="mr-1.5 text-base"></iconify-icon>
                            Xóa tài khoản
                        </button>
                    </div>
                </div>

                <div class="space-y-4 pt-6 border-t border-gray-100 dark:border-neutral-800">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Phương thức thanh toán</h2>
                        @if (session('status') === 'payment-method-deleted')
                            <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                Đã xóa phương thức thanh toán thành công.
                            </span>
                        @endif
                    </div>
                    
                    @php
                        $userPaymentMethods = $user->paymentMethods()->orderByDesc('is_default')->latest()->get();
                    @endphp

                    @forelse($userPaymentMethods as $pm)
                        <div class="border border-gray-100 dark:border-neutral-800 rounded-xl p-4 flex items-center gap-4 bg-white dark:bg-[#161b22]">
                            <div class="flex items-center gap-1.5 shrink-0 py-0.5">
                                @if($pm->type === 'momo')
                                    <iconify-icon icon="logos:momo" class="text-2xl"></iconify-icon>
                                @else
                                    <iconify-icon icon="logos:visa" class="text-2xl"></iconify-icon>
                                    <iconify-icon icon="logos:mastercard" class="text-2xl"></iconify-icon>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $pm->title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $pm->subtitle }}</p>
                            </div>
                            <div class="flex items-center gap-2.5">
                                @if($pm->is_default)
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">Mặc định</span>
                                @endif
                                <form method="POST" action="{{ route('profile.payment-methods.destroy', $pm->id) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phương thức thanh toán này?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition" title="Xóa phương thức thanh toán">
                                        <iconify-icon icon="lucide:trash-2" class="text-base block"></iconify-icon>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="border border-dashed border-gray-200 dark:border-neutral-800 rounded-xl p-6 text-center text-sm text-gray-500 dark:text-gray-400 bg-gray-50/50 dark:bg-neutral-900/30">
                            <iconify-icon icon="lucide:credit-card" class="text-2xl text-gray-400 mb-1.5"></iconify-icon>
                            <p class="font-medium text-gray-700 dark:text-gray-300">Chưa lưu phương thức thanh toán</p>
                        </div>
                    @endforelse

                    <a href="{{ route('courses.index') }}" class="w-full py-3 border border-dashed border-gray-300 dark:border-neutral-700 rounded-xl text-sm font-medium text-gray-500 hover:bg-gray-50 dark:hover:bg-neutral-800 flex items-center justify-center gap-2 transition">
                        <iconify-icon icon="lucide:plus" class="text-lg"></iconify-icon>
                        Khám phá khóa học để thêm phương thức thanh toán
                    </a>
                </div>

                <div class="space-y-4 pt-6 border-t border-gray-100 dark:border-neutral-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Lịch sử thanh toán & Ghi danh</h2>
                    @php
                        $recentEnrollments = $user->enrollments()->with('course')->latest()->take(5)->get();
                    @endphp

                    @if($recentEnrollments->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="border-b border-gray-100 dark:border-neutral-800 text-gray-400 font-medium text-xs">
                                    <tr>
                                        <th class="py-3 pr-4">Ngày</th>
                                        <th class="py-3 px-4">Khóa học</th>
                                        <th class="py-3 px-4">Số tiền</th>
                                        <th class="py-3 pl-4">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-neutral-800 text-xs">
                                    @foreach($recentEnrollments as $enrollment)
                                        <tr>
                                            <td class="py-3.5 pr-4 text-gray-500 dark:text-gray-400">{{ $enrollment->created_at?->format('d/m/Y') }}</td>
                                            <td class="py-3.5 px-4 font-medium text-gray-900 dark:text-white max-w-xs truncate">{{ $enrollment->course->title ?? 'Khóa học' }}</td>
                                            <td class="py-3.5 px-4 text-gray-700 dark:text-gray-300">
                                                {{ $enrollment->course && $enrollment->course->price > 0 ? number_format($enrollment->course->price) . ' đ' : 'Miễn phí' }}
                                            </td>
                                            <td class="py-3.5 pl-4">
                                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 text-[10px] font-bold rounded uppercase">
                                                    {{ $enrollment->status === 'paid' ? 'Đã thanh toán' : ($enrollment->status ?? 'Hoàn thành') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400 text-sm">
                            <p>Chưa có giao dịch thanh toán nào.</p>
                        </div>
                    @endif
                </div>
            </section>
            @endif
        </main>
    </div>
</div>
@endsection
