<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Quản Trị Hệ Thống') - Admin Control Panel</title>

    <!-- Fonts: Be Vietnam Pro (Đồng bộ font trang chủ) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Iconify -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        :root {
            --accent: #019D91;
            --accent-hover: #008b80;
            --sidebar-w: 260px;
            --sidebar-collapsed: 72px;
        }

        * {
            font-family: 'Be Vietnam Pro', sans-serif;
        }

        body {
            background-color: #f9fafb;
            font-family: 'Be Vietnam Pro', sans-serif;
        }

        /* Sidebar transitions */
        #admin-sidebar {
            width: var(--sidebar-w);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #admin-sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        #admin-sidebar.collapsed .nav-text,
        #admin-sidebar.collapsed .logo-text,
        #admin-sidebar.collapsed .user-info {
            opacity: 0;
            pointer-events: none;
            width: 0;
            overflow: hidden;
            white-space: nowrap;
        }

        #admin-sidebar.collapsed .nav-item {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        #admin-sidebar.collapsed .sidebar-divider-label {
            display: none;
        }

        #admin-sidebar.collapsed .brand-container {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        #admin-sidebar.collapsed .user-block {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        #admin-sidebar.collapsed .user-trigger-card {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
            gap: 0;
        }

        /* Khi sidebar thu nhỏ: menu tài khoản bung sang phải (flyout) với chiều rộng chuẩn, không bị bóp nghẹt */
        #admin-sidebar.collapsed .user-dropdown-menu {
            left: calc(100% + 0.5rem) !important;
            right: auto !important;
            bottom: 0.5rem !important;
            margin-bottom: 0 !important;
            width: 240px !important;
            min-width: 240px !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08);
        }

        /* Vùng đệm vô hình để chuột rê từ avatar sang popup không bị mất focus */
        #admin-sidebar.collapsed .user-dropdown-menu::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: -0.75rem;
            width: 0.75rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.625rem 1rem;
            margin: 0.15rem 0.625rem;
            border-radius: 0.5rem;
            color: #4b5563;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .nav-item:hover {
            background-color: #f3f4f6;
            color: var(--accent);
        }

        .nav-item.is-active {
            background-color: #e6f6f5;
            color: var(--accent);
            font-weight: 600;
        }

        .glass-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
        }

        .status-badge {
            padding: 0.2rem 0.625rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    </style>
</head>
<body class="h-full bg-[#f9fafb] antialiased text-gray-800" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen bg-[#f9fafb] overflow-hidden">

        <!-- Mobile Backdrop -->
        <div x-show="sidebarOpen"
             x-cloak
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"></div>

        <!-- ====== SIDEBAR ====== -->
        <aside id="admin-sidebar"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 flex shrink-0 flex-col bg-white border-r border-gray-200 transition-transform duration-200 lg:static lg:translate-x-0">

            <!-- Brand -->
            <div class="brand-container h-16 flex items-center px-5 border-b border-gray-100 shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 bg-[#019D91] rounded-lg flex items-center justify-center shrink-0 shadow-sm">
                        <iconify-icon icon="lucide:graduation-cap" class="text-white text-lg"></iconify-icon>
                    </div>
                    <span class="logo-text font-bold text-gray-900 tracking-tight text-base whitespace-nowrap transition-all duration-300">ADMIN CORE</span>
                </a>
                <!-- Mobile close button -->
                <button @click="sidebarOpen = false" class="ml-auto text-gray-400 hover:text-gray-700 lg:hidden">
                    <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 py-5 overflow-y-auto space-y-0.5">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-item {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                    <iconify-icon icon="lucide:layout-dashboard" class="text-xl shrink-0"></iconify-icon>
                    <span class="nav-text ml-3 whitespace-nowrap transition-all duration-300">Dashboard</span>
                </a>

                <!-- Người dùng -->
                <a href="{{ route('admin.users.index') }}"
                   class="nav-item {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
                    <iconify-icon icon="lucide:users" class="text-xl shrink-0"></iconify-icon>
                    <span class="nav-text ml-3 whitespace-nowrap transition-all duration-300">Người dùng</span>
                </a>

                <!-- Nội dung / Khóa học -->
                <a href="{{ route('admin.courses.index') }}"
                   class="nav-item {{ request()->routeIs('admin.courses.*') ? 'is-active' : '' }}">
                    <iconify-icon icon="lucide:book-open" class="text-xl shrink-0"></iconify-icon>
                    <span class="nav-text ml-3 whitespace-nowrap transition-all duration-300">Khóa học</span>
                </a>

                <!-- Danh mục -->
                <a href="{{ route('admin.categories.index') }}"
                   class="nav-item {{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}">
                    <iconify-icon icon="lucide:tag" class="text-xl shrink-0"></iconify-icon>
                    <span class="nav-text ml-3 whitespace-nowrap transition-all duration-300">Danh mục</span>
                </a>

                <!-- Ghi danh -->
                <a href="{{ route('admin.enrollments.index') }}"
                   class="nav-item {{ request()->routeIs('admin.enrollments.*') ? 'is-active' : '' }}">
                    <iconify-icon icon="lucide:clipboard-list" class="text-xl shrink-0"></iconify-icon>
                    <span class="nav-text ml-3 whitespace-nowrap transition-all duration-300">Ghi danh</span>
                </a>

                <!-- Đánh giá -->
                <a href="{{ route('admin.reviews.index') }}"
                   class="nav-item {{ request()->routeIs('admin.reviews.*') ? 'is-active' : '' }}">
                    <iconify-icon icon="lucide:star" class="text-xl shrink-0"></iconify-icon>
                    <span class="nav-text ml-3 whitespace-nowrap transition-all duration-300">Đánh giá</span>
                </a>

                <!-- Chi trả Giảng viên -->
                <a href="{{ route('admin.payouts.index') }}"
                   class="nav-item {{ request()->routeIs('admin.payouts.*') ? 'is-active' : '' }}">
                    <iconify-icon icon="lucide:wallet" class="text-xl shrink-0"></iconify-icon>
                    <span class="nav-text ml-3 whitespace-nowrap transition-all duration-300">Chi trả</span>
                </a>

                <!-- Divider -->
                <div class="pt-3 pb-1 px-5">
                    <div class="h-px bg-gray-100"></div>
                </div>

                <!-- Banners -->
                <a href="{{ route('admin.banners.index') }}"
                   class="nav-item {{ request()->routeIs('admin.banners.*') ? 'is-active' : '' }}">
                    <iconify-icon icon="lucide:image" class="text-xl shrink-0"></iconify-icon>
                    <span class="nav-text ml-3 whitespace-nowrap transition-all duration-300">Banners</span>
                </a>

                <!-- Thông báo -->
                <a href="{{ route('admin.notifications.index') }}"
                   class="nav-item {{ request()->routeIs('admin.notifications.*') ? 'is-active' : '' }}">
                    <iconify-icon icon="lucide:bell" class="text-xl shrink-0"></iconify-icon>
                    <span class="nav-text ml-3 whitespace-nowrap transition-all duration-300">Thông báo</span>
                </a>

                <!-- Phản hồi & Báo cáo -->
                <a href="{{ route('admin.feedbacks.index') }}"
                   class="nav-item {{ request()->routeIs('admin.feedbacks.*') ? 'is-active' : '' }}">
                    <iconify-icon icon="lucide:message-square-warning" class="text-xl shrink-0"></iconify-icon>
                    <span class="nav-text ml-3 whitespace-nowrap transition-all duration-300">Phản hồi & Báo cáo</span>
                </a>

                <!-- Về trang chủ -->
                <a href="{{ route('home') }}" class="nav-item">
                    <iconify-icon icon="lucide:arrow-left" class="text-xl shrink-0"></iconify-icon>
                    <span class="nav-text ml-3 whitespace-nowrap transition-all duration-300">Về trang chủ</span>
                </a>
            </nav>

            <!-- User Profile Footer with Hover / Click Popover -->
            <div class="user-block relative border-t border-gray-100 p-3 shrink-0"
                 x-data="{ userMenuOpen: false }"
                 @mouseenter="userMenuOpen = true"
                 @mouseleave="userMenuOpen = false">

                <!-- Popover Menu -->
                <div x-show="userMenuOpen"
                     x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="user-dropdown-menu absolute bottom-full left-3 right-3 mb-2 bg-white rounded-xl shadow-xl border border-gray-100 p-2 z-50 divide-y divide-gray-100"
                     @click.away="userMenuOpen = false">
                    <div class="px-3 py-2">
                        <p class="text-xs font-semibold text-gray-900 truncate">{{ Auth::user()->name ?? 'Quản Trị Viên' }}</p>
                        <p class="text-[11px] text-gray-500 truncate">{{ Auth::user()->email ?? 'admin@gmail.com' }}</p>
                    </div>
                    <div class="py-1">
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-gray-700 hover:bg-[#019D91]/10 hover:text-[#019D91] rounded-lg transition-colors whitespace-nowrap">
                            <iconify-icon icon="lucide:user-cog" class="text-base text-gray-400 shrink-0"></iconify-icon>
                            <span>Cài đặt tài khoản admin</span>
                        </a>
                    </div>
                    <div class="pt-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors whitespace-nowrap">
                                <iconify-icon icon="lucide:log-out" class="text-base shrink-0"></iconify-icon>
                                <span>Đăng xuất</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Profile Trigger Card -->
                <div @click="userMenuOpen = !userMenuOpen"
                     class="user-trigger-card flex items-center gap-2.5 p-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors group">
                    <div class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center font-bold text-sm shrink-0 overflow-hidden">
                        @if(Auth::user()->avatar_url)
                            <img src="{{ Auth::user()->avatar_url }}" referrerpolicy="no-referrer" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')">
                            <span class="hidden flex items-center justify-center w-full h-full">{{ Auth::user()->initialLetter() }}</span>
                        @else
                            <span>{{ Auth::user()->initialLetter() }}</span>
                        @endif
                    </div>
                    <div class="user-info min-w-0 flex-1 transition-all duration-300">
                        <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-[#019D91] transition-colors">{{ Auth::user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email ?? 'admin@system.com' }}</p>
                    </div>
                    <iconify-icon icon="lucide:chevron-up" class="nav-text text-gray-400 group-hover:text-gray-600 text-sm transition-all"></iconify-icon>
                </div>
            </div>
        </aside>

        <!-- ====== MAIN CONTENT ====== -->
        <div class="flex flex-1 flex-col min-w-0 overflow-hidden">

            <!-- Header -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-40">
                <div class="flex items-center gap-3">
                    <!-- Toggle Sidebar (Desktop) -->
                    <button id="toggle-sidebar"
                            class="hidden lg:flex p-2 hover:bg-gray-100 rounded-lg text-gray-500 transition-colors">
                        <iconify-icon icon="lucide:menu" class="text-xl"></iconify-icon>
                    </button>
                    <!-- Toggle Sidebar (Mobile) -->
                    <button @click="sidebarOpen = true"
                            class="flex lg:hidden p-2 hover:bg-gray-100 rounded-lg text-gray-500 transition-colors">
                        <iconify-icon icon="lucide:menu" class="text-xl"></iconify-icon>
                    </button>
                    <h1 class="text-base font-bold text-gray-800 truncate uppercase tracking-wide">
                        @yield('page_title', 'Bảng Quản Trị Hệ Thống')
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Notification Bell -->
                    @include('partials.notification-bell')

                    <div class="h-6 w-px bg-gray-200"></div>

                    <!-- User Avatar Only -->
                    <div class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full bg-black text-sm font-bold text-white shadow-sm ring-2 ring-gray-100 select-none" title="{{ Auth::user()->name ?? 'Admin' }}">
                        @if(Auth::user()->avatar_url)
                            <img src="{{ Auth::user()->avatar_url }}" referrerpolicy="no-referrer" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')">
                            <span class="hidden flex items-center justify-center w-full h-full">{{ Auth::user()->initialLetter() }}</span>
                        @else
                            <span>{{ Auth::user()->initialLetter() }}</span>
                        @endif
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main id="admin-main-content" data-back-to-top-scroll class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-[#f9fafb]">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2.5 shadow-sm">
                        <iconify-icon icon="lucide:check-circle" class="text-emerald-600 shrink-0 text-lg"></iconify-icon>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    @include('partials.error-alert', ['message' => session('error')])
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm shadow-sm">
                        <p class="font-semibold mb-1 flex items-center gap-2">
                            <iconify-icon icon="lucide:alert-triangle" class="text-red-600 text-lg"></iconify-icon>
                            Vui lòng kiểm tra lại dữ liệu:
                        </p>
                        <ul class="list-disc list-inside space-y-0.5 text-xs ml-6">
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

    @include('partials.admin-confirm-modal')
    @include('partials.back-to-top')

    @stack('scripts')

    <script>
        // Desktop sidebar toggle
        const sidebar = document.getElementById('admin-sidebar');
        const toggleBtn = document.getElementById('toggle-sidebar');
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
            });
        }
    </script>
</body>
</html>
