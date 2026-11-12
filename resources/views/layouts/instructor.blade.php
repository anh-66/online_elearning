<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-neutral-50 dark:bg-neutral-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.theme-script')

    <title>@yield('title', 'Studio Giảng viên') - {{ config('app.name', 'EduLearn') }}</title>

    <!-- Preconnect Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        *, body, button, input, textarea, select {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
</head>
<body class="h-full bg-white font-sans antialiased text-neutral-800 dark:bg-neutral-950 dark:text-neutral-200"
      x-data="instructorSidebar()"
      x-cloak>

    <div data-slot="sidebar-wrapper" class="flex h-screen w-full overflow-hidden">
        
        {{-- Mobile Backdrop --}}
        <div x-show="openMobile"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="openMobile = false"
             class="fixed inset-0 z-40 bg-black/50 backdrop-blur-xs lg:hidden">
        </div>

        {{-- ===== ANIMATE-UI COLLAPSIBLE SIDEBAR ===== --}}
        <aside id="instructor-sidebar"
               data-slot="sidebar"
               :data-state="isCollapsed ? 'collapsed' : 'expanded'"
               class="fixed inset-y-0 left-0 z-50 flex h-full flex-col border-r border-neutral-200 bg-white text-neutral-700 transition-[width,transform] duration-300 ease-[cubic-bezier(0.2,0,0,1)] dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-300 lg:static lg:inset-auto shrink-0 select-none"
               :class="{
                   'w-64': !isCollapsed,
                   'w-[4.25rem]': isCollapsed,
                   'translate-x-0': openMobile,
                   '-translate-x-full lg:translate-x-0': !openMobile
               }">
            
            {{-- Interactive Sidebar Rail (Resize/Toggle Edge) --}}
            <div @click="toggleSidebar()"
                 data-sidebar="rail"
                 class="group/rail absolute -right-1.5 inset-y-0 z-20 hidden w-3 cursor-ew-resize sm:flex items-center justify-center"
                 title="Nhấp để thu nhỏ / mở rộng sidebar">
                <div class="h-full w-[2px] bg-transparent group-hover/rail:bg-neutral-300 dark:group-hover/rail:bg-neutral-700 transition-colors"></div>
            </div>

            {{-- Sidebar Header / TeamSwitcher --}}
            <div data-slot="sidebar-header" class="p-3 border-b border-neutral-100 dark:border-neutral-800/80">
                <a href="{{ route('instructor.dashboard') }}"
                   class="group/brand flex items-center gap-3 p-1.5 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800/70 transition-colors outline-none"
                   :class="isCollapsed ? 'justify-center' : ''">
                    <div class="size-8 rounded-lg bg-neutral-900 text-white dark:bg-white dark:text-neutral-950 font-black text-xs flex items-center justify-center shrink-0 shadow-xs tracking-wider">
                        IN
                    </div>
                    <div x-show="!isCollapsed" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-x-1"
                         x-transition:enter-end="opacity-100 translate-x-0"
                         class="min-w-0 flex-1 text-left">
                        <div class="truncate text-sm font-bold text-neutral-900 dark:text-white leading-tight">
                            Studio Giảng Viên
                        </div>
                        <div class="truncate text-[11px] font-medium text-neutral-500 dark:text-neutral-400">
                            Instructor Portal
                        </div>
                    </div>
                </a>
            </div>

            {{-- Sidebar Content (Scrollable Navigation) --}}
            <div data-slot="sidebar-content" class="flex-1 overflow-y-auto overflow-x-hidden p-2 space-y-4 no-scrollbar">
                
                {{-- Group 1: Khóa học & Nội dung --}}
                <div data-slot="sidebar-group" class="space-y-1">
                    <div x-show="!isCollapsed"
                         class="px-3 pt-1 pb-1.5 text-[10px] font-bold tracking-wider text-neutral-400 dark:text-neutral-500 uppercase">
                        Quản lý khóa học
                    </div>

                    {{-- 1. Dashboard --}}
                    <div class="relative group/nav-item">
                        <a href="{{ route('instructor.dashboard') }}"
                           data-slot="sidebar-menu-button"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('instructor/dashboard') ? 'bg-neutral-100 text-neutral-900 dark:bg-neutral-800 dark:text-white font-medium' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white' }}"
                           :class="isCollapsed ? 'justify-center px-0 size-9 mx-auto' : ''">
                            <iconify-icon icon="lucide:layout-dashboard" class="text-lg shrink-0"></iconify-icon>
                            <span x-show="!isCollapsed" class="truncate">Tổng quan</span>
                        </a>

                        {{-- Floating Tooltip when collapsed --}}
                        <div x-show="isCollapsed"
                             class="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-2 z-50 hidden group-hover/nav-item:flex items-center px-2.5 py-1 text-xs font-medium text-white bg-neutral-900 dark:bg-neutral-800 rounded-md shadow-lg whitespace-nowrap">
                            Tổng quan
                        </div>
                    </div>

                    {{-- 2. Collapsible: Khóa học --}}
                    <div class="relative group/nav-item">
                        <button type="button"
                                @click="toggleSubmenu('courses')"
                                data-slot="sidebar-menu-button"
                                class="w-full flex items-center justify-between gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('instructor/courses*', 'instructor/chapters*', 'instructor/lessons*', 'instructor/quizzes*', 'instructor/questions*') ? 'text-neutral-900 dark:text-white font-medium' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white' }}"
                                :class="isCollapsed ? 'justify-center px-0 size-9 mx-auto' : ''">
                            <div class="flex items-center gap-3 min-w-0">
                                <iconify-icon icon="lucide:book-open" class="text-lg shrink-0"></iconify-icon>
                                <span x-show="!isCollapsed" class="truncate">Khóa học của tôi</span>
                            </div>
                            <svg x-show="!isCollapsed"
                                 class="size-4 shrink-0 transition-transform duration-200 text-neutral-400"
                                 :class="openSubmenus.courses ? 'rotate-180 text-neutral-800 dark:text-white' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- Tooltip when collapsed --}}
                        <div x-show="isCollapsed"
                             class="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-2 z-50 hidden group-hover/nav-item:flex items-center px-2.5 py-1 text-xs font-medium text-white bg-neutral-900 dark:bg-neutral-800 rounded-md shadow-lg whitespace-nowrap">
                            Khóa học của tôi
                        </div>

                        {{-- Submenu Accordion --}}
                        <div x-show="!isCollapsed && openSubmenus.courses"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             data-slot="sidebar-menu-sub"
                             class="ml-5 pl-3 border-l border-neutral-200 dark:border-neutral-800 my-1 space-y-1">
                            <a href="{{ route('instructor.courses.index') }}"
                               class="block px-2.5 py-1.5 rounded-lg text-sm font-medium transition-colors {{ request()->is('instructor/courses') ? 'bg-neutral-100 text-neutral-900 dark:bg-neutral-800 dark:text-white font-medium' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white' }}">
                                Tất cả khóa học
                            </a>
                            <a href="{{ route('instructor.courses.create') }}"
                               class="block px-2.5 py-1.5 rounded-lg text-sm font-medium transition-colors {{ request()->is('instructor/courses/create') ? 'bg-neutral-100 text-neutral-900 dark:bg-neutral-800 dark:text-white font-medium' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white' }}">
                                Tạo khóa học mới
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Group 2: Học viên & Tài chính --}}
                <div data-slot="sidebar-group" class="space-y-1">
                    <div x-show="!isCollapsed"
                         class="px-3 pt-2 pb-1.5 text-[10px] font-bold tracking-wider text-neutral-400 dark:text-neutral-500 uppercase">
                        Học viên & Doanh thu
                    </div>

                    {{-- Quản lý Học viên --}}
                    <div class="relative group/nav-item">
                        <a href="{{ route('instructor.students.index') }}"
                           data-slot="sidebar-menu-button"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('instructor/students*') ? 'bg-neutral-100 text-neutral-900 dark:bg-neutral-800 dark:text-white font-medium' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white' }}"
                           :class="isCollapsed ? 'justify-center px-0 size-9 mx-auto' : ''">
                            <iconify-icon icon="lucide:users" class="text-lg shrink-0"></iconify-icon>
                            <span x-show="!isCollapsed" class="truncate">Quản lý Học viên</span>
                        </a>

                        <div x-show="isCollapsed"
                             class="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-2 z-50 hidden group-hover/nav-item:flex items-center px-2.5 py-1 text-xs font-medium text-white bg-neutral-900 dark:bg-neutral-800 rounded-md shadow-lg whitespace-nowrap">
                            Quản lý Học viên
                        </div>
                    </div>

                    {{-- Thu nhập & Rút tiền --}}
                    <div class="relative group/nav-item">
                        <a href="{{ route('instructor.payouts.index') }}"
                           data-slot="sidebar-menu-button"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('instructor/payouts*') ? 'bg-neutral-100 text-neutral-900 dark:bg-neutral-800 dark:text-white font-medium' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white' }}"
                           :class="isCollapsed ? 'justify-center px-0 size-9 mx-auto' : ''">
                            <iconify-icon icon="lucide:wallet" class="text-lg shrink-0"></iconify-icon>
                            <span x-show="!isCollapsed" class="truncate">Thu nhập & Rút tiền</span>
                        </a>

                        <div x-show="isCollapsed"
                             class="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-2 z-50 hidden group-hover/nav-item:flex items-center px-2.5 py-1 text-xs font-medium text-white bg-neutral-900 dark:bg-neutral-800 rounded-md shadow-lg whitespace-nowrap">
                            Thu nhập & Rút tiền
                        </div>
                    </div>
                </div>

                {{-- Group 3: Hệ thống --}}
                <div data-slot="sidebar-group" class="space-y-1">
                    <div x-show="!isCollapsed"
                         class="px-3 pt-2 pb-1.5 text-[10px] font-bold tracking-wider text-neutral-400 dark:text-neutral-500 uppercase">
                        Hệ thống
                    </div>

                    {{-- Khóa học của tôi (Học viên) --}}
                    <div class="relative group/nav-item">
                        <a href="{{ route('dashboard') }}"
                           data-slot="sidebar-menu-button"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('dashboard', 'my-courses') ? 'bg-neutral-100 text-neutral-900 dark:bg-neutral-800 dark:text-white font-medium' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white' }}"
                           :class="isCollapsed ? 'justify-center px-0 size-9 mx-auto' : ''">
                            <iconify-icon icon="lucide:graduation-cap" class="text-lg shrink-0"></iconify-icon>
                            <span x-show="!isCollapsed" class="truncate">Giao diện Học viên</span>
                        </a>

                        <div x-show="isCollapsed"
                             class="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-2 z-50 hidden group-hover/nav-item:flex items-center px-2.5 py-1 text-xs font-medium text-white bg-neutral-900 dark:bg-neutral-800 rounded-md shadow-lg whitespace-nowrap">
                            Giao diện Học viên
                        </div>
                    </div>

                    {{-- Về trang chủ Public --}}
                    <div class="relative group/nav-item">
                        <a href="{{ url('/') }}"
                           data-slot="sidebar-menu-button"
                           class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white transition-colors"
                           :class="isCollapsed ? 'justify-center px-0 size-9 mx-auto' : ''">
                            <iconify-icon icon="lucide:globe" class="text-lg shrink-0"></iconify-icon>
                            <span x-show="!isCollapsed" class="truncate">Trang chủ EduLearn</span>
                        </a>

                        <div x-show="isCollapsed"
                             class="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-2 z-50 hidden group-hover/nav-item:flex items-center px-2.5 py-1 text-xs font-medium text-white bg-neutral-900 dark:bg-neutral-800 rounded-md shadow-lg whitespace-nowrap">
                            Trang chủ EduLearn
                        </div>
                    </div>
                </div>

            </div>

            {{-- Sidebar Footer (NavUser with Flyout Menu) --}}
            <div data-slot="sidebar-footer" 
                 class="relative p-2.5 border-t border-neutral-100 dark:border-neutral-800/80 bg-white dark:bg-neutral-900">
                
                {{-- Dropdown Menu (Flyout right when collapsed, Popup top when expanded) --}}
                <div x-show="userMenuOpen"
                     @click.outside="userMenuOpen = false"
                     @keydown.escape.window="userMenuOpen = false"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute z-50 w-56 p-1.5 rounded-xl border border-neutral-200 bg-white shadow-xl ring-1 ring-black/5 dark:border-neutral-800 dark:bg-neutral-900 dark:ring-white/5 space-y-0.5"
                     :class="isCollapsed ? 'left-[calc(100%+0.5rem)] bottom-1' : 'bottom-full left-2 right-2 mb-2 w-auto'">
                    
                    <div class="px-2.5 py-2 border-b border-neutral-100 dark:border-neutral-800 mb-1">
                        <p class="text-xs font-semibold text-neutral-900 dark:text-white truncate">{{ Auth::user()->name ?? 'Giảng viên' }}</p>
                        <p class="text-[11px] text-neutral-500 dark:text-neutral-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                    </div>

                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center gap-2.5 px-2.5 py-2 text-xs font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors">
                        <iconify-icon icon="lucide:user" class="text-base text-neutral-400"></iconify-icon>
                        <span>Hồ sơ cá nhân</span>
                    </a>

                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-2.5 px-2.5 py-2 text-xs font-medium text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg transition-colors">
                        <iconify-icon icon="lucide:book-open-check" class="text-base text-neutral-400"></iconify-icon>
                        <span>Góc học viên</span>
                    </a>

                    <div class="h-px bg-neutral-100 dark:bg-neutral-800 my-1"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center gap-2.5 px-2.5 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-lg transition-colors text-left">
                            <iconify-icon icon="lucide:log-out" class="text-base text-rose-500"></iconify-icon>
                            <span>Đăng xuất</span>
                        </button>
                    </form>
                </div>

                {{-- User Trigger Button --}}
                <button type="button"
                        @click="userMenuOpen = !userMenuOpen"
                        data-slot="sidebar-menu-button"
                        class="w-full flex items-center gap-2.5 p-2 rounded-xl hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors text-left outline-none"
                        :class="isCollapsed ? 'justify-center p-1' : ''">
                    <div class="size-8 rounded-lg bg-neutral-900 text-white dark:bg-white dark:text-neutral-950 font-bold flex items-center justify-center shrink-0 overflow-hidden text-xs">
                        @if(Auth::user()->avatar_url)
                            <img src="{{ Auth::user()->avatar_url }}" referrerpolicy="no-referrer" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')">
                            <span class="hidden flex items-center justify-center w-full h-full">{{ Auth::user()->initialLetter() }}</span>
                        @else
                            <span>{{ Auth::user()->initialLetter() }}</span>
                        @endif
                    </div>
                    
                    <div x-show="!isCollapsed" class="min-w-0 flex-1">
                        <div class="truncate text-xs font-semibold text-neutral-900 dark:text-white leading-tight">
                            {{ Auth::user()->name ?? 'Giảng viên' }}
                        </div>
                        <div class="truncate text-[11px] text-neutral-500 dark:text-neutral-400">
                            {{ Auth::user()->email ?? '' }}
                        </div>
                    </div>

                    <svg x-show="!isCollapsed" class="size-4 shrink-0 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                    </svg>
                </button>
            </div>

        </aside>

        {{-- ===== MAIN INSET COLUMN ===== --}}
        <div data-slot="sidebar-inset" class="flex-1 flex flex-col min-w-0 overflow-hidden bg-neutral-50 dark:bg-neutral-950">
            
            {{-- Top Navbar with Animate-UI SidebarTrigger --}}
            <header class="h-16 shrink-0 bg-white dark:bg-neutral-900 border-b border-neutral-200 dark:border-neutral-800 flex items-center justify-between px-4 sm:px-6 shadow-xs">
                <div class="flex items-center gap-3 min-w-0">
                    {{-- Desktop Sidebar Trigger (PanelLeft Animate UI Icon) --}}
                    <button type="button"
                            @click="toggleSidebar()"
                            data-slot="sidebar-trigger"
                            data-sidebar="trigger"
                            class="hidden lg:flex size-9 items-center justify-center rounded-lg border border-neutral-200 dark:border-neutral-800 text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors shadow-xs"
                            title="Thu gọn / Mở rộng Sidebar">
                        <iconify-icon icon="lucide:panel-left" class="text-lg transition-transform duration-200" :class="isCollapsed ? 'rotate-180' : ''"></iconify-icon>
                        <span class="sr-only">Toggle Sidebar</span>
                    </button>

                    {{-- Mobile Hamburger Trigger --}}
                    <button type="button"
                            @click="openMobile = true"
                            class="lg:hidden size-9 flex items-center justify-center rounded-lg border border-neutral-200 dark:border-neutral-800 text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white">
                        <iconify-icon icon="lucide:menu" class="text-xl"></iconify-icon>
                    </button>

                    <h1 class="text-base sm:text-lg font-bold text-neutral-900 dark:text-white truncate">
                        @yield('page_title', 'Studio Giảng viên')
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    @include('partials.notification-bell')
                    <x-theme-toggle />
                </div>
            </header>

            {{-- Main Scrollable Content --}}
            <main data-back-to-top-scroll class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 min-w-0 bg-white dark:bg-neutral-950">
                @if($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 text-sm shadow-xs">
                        <p class="font-semibold mb-1">Vui lòng kiểm tra lại các trường lỗi:</p>
                        <ul class="list-disc list-inside space-y-0.5 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    @include('partials.back-to-top')
    @include('partials.toaster')

    <script>
        function instructorSidebar() {
            return {
                isCollapsed: localStorage.getItem('instructor_sidebar_collapsed') === 'true',
                openMobile: false,
                userMenuOpen: false,
                openSubmenus: {
                    courses: {{ request()->is('instructor/courses*', 'instructor/chapters*', 'instructor/lessons*', 'instructor/quizzes*', 'instructor/questions*') ? 'true' : 'false' }}
                },

                toggleSidebar() {
                    this.isCollapsed = !this.isCollapsed;
                    localStorage.setItem('instructor_sidebar_collapsed', this.isCollapsed);
                },

                toggleSubmenu(key) {
                    if (this.isCollapsed) {
                        this.isCollapsed = false;
                        localStorage.setItem('instructor_sidebar_collapsed', 'false');
                        this.openSubmenus[key] = true;
                    } else {
                        this.openSubmenus[key] = !this.openSubmenus[key];
                    }
                }
            };
        }
    </script>
</body>
</html>
