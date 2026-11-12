<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'EduLearn')) - Nền tảng Học trực tuyến</title>

    @include('partials.theme-script')

    <!-- Preconnect Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="flex min-h-screen flex-col bg-white text-[#1D1D1F] dark:bg-[#0d1117] dark:text-[#e6edf3] antialiased selection:bg-[#019D91] selection:text-white"
      data-back-to-top-scroll
      x-data="{ 
          mobileMenuOpen: false, 
          activeMenu: null, 
          menuTimeout: null, 
          openMenu(menu) { 
              clearTimeout(this.menuTimeout); 
              this.activeMenu = menu; 
          }, 
          closeMenu() { 
              this.menuTimeout = setTimeout(() => { this.activeMenu = null; }, 180); 
          }
      }">
    <!-- Header / Navbar (Nền đặc hoàn toàn, không trong suốt) -->
    <header class="sticky top-0 z-50 bg-white dark:bg-[#161b22] border-b border-[#E5E5EA] dark:border-[#30363d] transition-colors shadow-xs"
            @keydown.escape="activeMenu = null; clearTimeout(menuTimeout)">
        <div class="w-full max-w-screen-2xl mx-auto px-6 sm:px-10 lg:px-14">
            <div class="flex items-center justify-between h-18 sm:h-20">
                <!-- Left: Logo & Nav Links -->
                <div class="flex items-center space-x-6 lg:space-x-8 xl:space-x-10 shrink-0">
                    <a href="{{ url('/') }}" class="flex items-center space-x-3 text-[#1D1D1F] dark:text-white group shrink-0">
                        <svg class="w-8 h-8 text-[#019D91] dark:text-[#00ddb3] group-hover:scale-105 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                        <span class="text-xl sm:text-2xl font-bold tracking-tight whitespace-nowrap">EduLearn</span>
                    </a>

                    <!-- Desktop Navigation Menu (@animate-ui / LottieFiles NavigationMenu Style) -->
                    <nav class="hidden md:flex items-center gap-1.5 shrink-0 relative"
                         data-slot="navigation-menu"
                         @mouseleave="closeMenu()">
                        <a href="{{ url('/') }}"
                           class="h-9 px-3.5 py-1.5 rounded-lg text-[15px] font-semibold transition-all inline-flex items-center justify-center outline-none select-none text-[#1D1D1F] dark:text-[#e6edf3] hover:bg-black/[0.05] dark:hover:bg-white/10">
                            Trang chủ
                        </a>

                        <a href="{{ url('/courses') }}"
                           class="h-9 px-3.5 py-1.5 rounded-lg text-[15px] font-semibold transition-all inline-flex items-center justify-center outline-none select-none text-[#1D1D1F] dark:text-[#e6edf3] hover:bg-black/[0.05] dark:hover:bg-white/10">
                            Khóa học
                        </a>

                        <!-- Support Trigger -->
                        <div class="relative shrink-0">
                            <button type="button"
                                    @mouseenter="openMenu('support')"
                                    @click="activeMenu = activeMenu === 'support' ? null : 'support'"
                                    class="h-9 px-3.5 py-1.5 rounded-lg text-[15px] font-semibold transition-all inline-flex items-center justify-center gap-1.5 outline-none select-none group hover:bg-black/[0.05] dark:hover:bg-white/10 text-[#1D1D1F] dark:text-[#e6edf3]"
                                    :class="activeMenu === 'support' ? 'bg-black/[0.05] dark:bg-white/10' : ''">
                                <span>Hỗ Trợ</span>
                                <svg class="relative top-px size-3 text-gray-400 dark:text-gray-400 transition-transform duration-300 ease-out"
                                     :class="{ 'rotate-180': activeMenu === 'support' }"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>

                        <!-- Features Trigger -->
                        <div class="relative shrink-0">
                            <button type="button"
                                    @mouseenter="openMenu('features')"
                                    @click="activeMenu = activeMenu === 'features' ? null : 'features'"
                                    class="h-9 px-3.5 py-1.5 rounded-lg text-[15px] font-semibold transition-all inline-flex items-center justify-center gap-1.5 outline-none select-none group hover:bg-black/[0.05] dark:hover:bg-white/10 text-[#1D1D1F] dark:text-[#e6edf3]"
                                    :class="activeMenu === 'features' ? 'bg-black/[0.05] dark:bg-white/10' : ''">
                                <span>Tiện ích</span>
                                <svg class="relative top-px size-3 text-gray-400 dark:text-gray-400 transition-transform duration-300 ease-out"
                                     :class="{ 'rotate-180': activeMenu === 'features' }"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>

                        <!-- Shared Viewport Dropdown Menu (Cố định ô to, mượt mà khi chuyển menu) -->
                        @include('partials.navigation-menu-dropdown')
                    </nav>
                </div>

                <!-- Center: Compact Search Form (Thu nhỏ gọn gàng, dãn đều navbar) -->
                <div class="hidden lg:flex items-center w-36 lg:w-44 xl:w-56 shrink mx-3 lg:mx-5">
                    <form action="{{ url('/courses') }}" method="GET" class="w-full relative">
                        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm kiếm..."
                               class="w-full h-10 pl-10 pr-4 text-[13px] lg:text-[14px] bg-black/[0.04] dark:bg-[#161b22] text-[#1D1D1F] dark:text-[#e6edf3] placeholder-[#86868B] dark:placeholder-[#7d8590] border border-transparent dark:border-[#30363d] focus:border-[#019D91]/40 dark:focus:border-[#02b3a5] rounded-full focus:ring-2 focus:ring-[#019D91]/20 transition">
                        <span class="absolute left-3.5 top-3 text-[#86868B] dark:text-[#7d8590]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                    </form>
                </div>

                <!-- Right: Actions (Notification Bell + Theme Toggle + User Dropdown / Auth Buttons) -->
                <div class="flex items-center space-x-3 sm:space-x-4 shrink-0">
                    @include('partials.notification-bell')

                    @auth
                        @if(Auth::user()->isStudent())
                        @include('partials.attendance-checkin')
                        @endif
                    @endauth

                    <x-theme-toggle class="!text-[#1D1D1F] dark:!text-[#e6edf3] hover:!bg-black/5 dark:hover:!bg-[#21262d] rounded-full p-2.5 transition" />

                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="hidden xl:inline-block whitespace-nowrap text-[15px] font-semibold text-[#1D1D1F] dark:text-[#e6edf3] transition px-3 py-1.5 rounded-xl hover:bg-black/5 dark:hover:bg-[#21262d]">
                                Trang Quản trị Admin
                            </a>
                        @elseif(Auth::user()->isInstructor())
                            <a href="{{ route('dashboard') }}" class="hidden xl:inline-block whitespace-nowrap text-[15px] font-semibold text-[#1D1D1F] dark:text-[#e6edf3] transition px-3 py-1.5 rounded-xl hover:bg-black/5 dark:hover:bg-[#21262d]">
                                Khóa học của tôi
                            </a>
                            <a href="{{ route('instructor.dashboard') }}" class="hidden xl:inline-block whitespace-nowrap text-[15px] font-semibold text-[#1D1D1F] dark:text-[#e6edf3] transition px-3 py-1.5 rounded-xl hover:bg-black/5 dark:hover:bg-[#21262d]">
                                Studio Giảng viên
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="hidden xl:inline-block whitespace-nowrap text-[15px] font-semibold text-[#1D1D1F] dark:text-[#e6edf3] transition px-3 py-1.5 rounded-xl hover:bg-black/5 dark:hover:bg-[#21262d]">
                                Khóa học của tôi
                            </a>
                        @endif

                        <!-- User Profile Dropdown Popover (@animate-ui / shadcn Popover Style) -->
                        <div class="relative" data-slot="popover" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open"
                                    data-slot="popover-trigger"
                                    :aria-expanded="open"
                                    class="flex items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-[#019D91] transition hover:opacity-90 active:scale-95"
                                    aria-label="Tài khoản cá nhân">
                                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-black text-white dark:bg-black dark:text-white font-bold text-base sm:text-lg flex items-center justify-center overflow-hidden ring-2 ring-black/10 dark:ring-[#30363d] shadow-sm select-none">
                                    @if(Auth::user()->avatar_url)
                                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')">
                                        <span class="hidden flex items-center justify-center w-full h-full">{{ Auth::user()->initialLetter() }}</span>
                                    @else
                                        <span>{{ Auth::user()->initialLetter() }}</span>
                                    @endif
                                </div>
                            </button>

                            <!-- Popover Content -->
                            <div x-show="open" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="transform opacity-0 -translate-y-1 scale-95"
                                 x-transition:enter-end="transform opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="transform opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="transform opacity-0 -translate-y-1 scale-95"
                                 data-slot="popover-content"
                                 class="absolute right-0 mt-3 w-64 bg-white dark:bg-[#161b22] text-[#1D1D1F] dark:text-[#e6edf3] rounded-2xl shadow-xl border border-neutral-200 dark:border-neutral-800 ring-1 ring-black/5 dark:ring-white/5 py-2 z-50 text-[15px] outline-hidden origin-top-right">

                                <div data-slot="popover-header" class="px-4 py-3 border-b border-neutral-100 dark:border-[#30363d]">
                                    <p data-slot="popover-title" class="font-bold truncate text-neutral-900 dark:text-white">{{ Auth::user()->name }}</p>
                                    <p data-slot="popover-description" class="text-xs text-[#86868B] dark:text-[#7d8590] truncate mt-0.5">{{ Auth::user()->email }}</p>
                                    <span class="inline-block mt-2 px-2.5 py-0.5 text-xs font-semibold rounded-full
                                        {{ Auth::user()->isAdmin() ? 'bg-neutral-900 text-white dark:bg-[#30363d] dark:text-white' : (Auth::user()->isInstructor() ? 'bg-[#019D91]/15 text-[#018076] dark:bg-[#019D91]/20 dark:text-[#02b3a5]' : 'bg-neutral-100 text-neutral-800 dark:bg-[#21262d] dark:text-[#c9d1d9]') }}">
                                        {{ Auth::user()->isAdmin() ? 'Quản trị viên' : (Auth::user()->isInstructor() ? 'Giảng viên' : 'Học viên') }}
                                    </span>
                                </div>

                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 hover:bg-[#F5F5F7] dark:hover:bg-[#21262d] transition font-semibold text-[#019D91] dark:text-[#02b3a5]">
                                        Trang Quản trị Admin
                                    </a>
                                @elseif(Auth::user()->isInstructor())
                                    <a href="{{ route('instructor.dashboard') }}" class="flex items-center px-4 py-2.5 hover:bg-[#F5F5F7] dark:hover:bg-[#21262d] transition font-semibold text-[#019D91] dark:text-[#02b3a5]">
                                        Studio Giảng viên
                                    </a>
                                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2.5 hover:bg-[#F5F5F7] dark:hover:bg-[#21262d] transition font-medium">
                                        Khóa học của tôi
                                    </a>
                                @else
                                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2.5 hover:bg-[#F5F5F7] dark:hover:bg-[#21262d] transition font-medium">
                                        Khóa học của tôi
                                    </a>
                                @endif

                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 hover:bg-[#F5F5F7] dark:hover:bg-[#21262d] transition font-medium">
                                    Hồ sơ cá nhân
                                </a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center px-4 py-2.5 text-rose-600 dark:text-rose-400 hover:bg-[#F5F5F7] dark:hover:bg-[#21262d] transition border-t border-[#E5E5EA] dark:border-[#30363d] font-medium">
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Compact Mobile 'Đăng nhập' Pill (< sm) -->
                        <a href="{{ route('login') }}"
                           class="sm:hidden inline-flex items-center justify-center h-9 px-3.5 rounded-xl bg-[#019D91] hover:bg-[#008b80] text-white text-xs font-semibold shadow-sm transition active:scale-95 shrink-0"
                           aria-label="Đăng nhập">
                            Đăng nhập
                        </a>

                        <!-- Single Uiverse Interactive 'Đăng nhập' Button (>= sm) -->
                        <a href="{{ route('login') }}"
                           class="hidden sm:inline-flex relative items-center justify-center h-11 sm:h-12 w-36 sm:w-40 rounded-2xl bg-white dark:bg-[#161b22] border border-[#E5E5EA] dark:border-[#30363d] text-[15px] font-bold text-[#1D1D1F] dark:text-[#f0f6fc] overflow-hidden group shadow-sm transition-all hover:shadow hover:border-[#019D91]/40 active:scale-[0.98]">
                            <!-- Expanding Teal Box with Arrow (centers when expanded) -->
                            <div class="absolute left-1 top-[3px] bottom-[3px] w-9 rounded-xl bg-[#019D91] flex items-center justify-center text-white transition-all duration-300 ease-out group-hover:w-[calc(100%-8px)] z-10 shadow-sm">
                                <svg class="w-4 h-4 text-white transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </div>
                            <!-- Text disappears completely on hover leaving only the arrow -->
                            <span class="pl-8 transition-all duration-200 group-hover:opacity-0 group-hover:scale-90 select-none">Đăng nhập</span>
                        </a>
                    @endauth

                    <!-- Mobile Menu Hamburger -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-[#1D1D1F] dark:text-white p-2 rounded-lg hover:bg-black/5 dark:hover:bg-[#21262d]" aria-label="Mở menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation Drawer -->
            <div x-show="mobileMenuOpen" x-cloak class="md:hidden border-t border-[#E5E5EA] dark:border-[#30363d] py-4 space-y-3">
                <form action="{{ url('/courses') }}" method="GET" class="mb-3">
                    <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm kiếm khóa học..."
                           class="w-full h-11 text-[15px] bg-black/[0.04] dark:bg-[#161b22] text-[#1D1D1F] dark:text-[#e6edf3] placeholder-[#86868B] dark:placeholder-[#7d8590] border border-transparent dark:border-[#30363d] rounded-2xl px-4 focus:ring-1 focus:ring-[#019D91]">
                </form>
                <a href="{{ url('/') }}" class="block px-3 py-2 rounded-xl text-base font-medium hover:bg-black/5 dark:hover:bg-[#21262d]">Trang chủ</a>
                <a href="{{ url('/courses') }}" class="block px-3 py-2 rounded-xl text-base font-medium hover:bg-black/5 dark:hover:bg-[#21262d]">Khóa học</a>
                <a href="{{ route('support.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium hover:bg-black/5 dark:hover:bg-[#21262d] {{ request()->routeIs('support.*') ? 'text-[#019D91] font-semibold' : '' }}">Hỗ Trợ</a>
                <div x-data="{ mobileFeaturesOpen: false }">
                    <button @click="mobileFeaturesOpen = !mobileFeaturesOpen" class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-base font-medium hover:bg-black/5 dark:hover:bg-[#21262d]">
                        <span>Tiện ích</span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': mobileFeaturesOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="mobileFeaturesOpen" x-cloak class="pl-3 pr-2 py-1 space-y-1">
                        @if(auth()->check() && Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm text-[#019D91] dark:text-[#02b3a5] font-semibold hover:bg-black/5 dark:hover:bg-[#21262d]">
                                Trang Quản trị Admin
                            </a>
                        @elseif(auth()->check() && Auth::user()->isInstructor())
                            <a href="{{ route('instructor.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm text-[#019D91] dark:text-[#02b3a5] font-semibold hover:bg-black/5 dark:hover:bg-[#21262d]">
                                Studio Giảng viên
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm text-[#1D1D1F] dark:text-[#f0f6fc] hover:bg-black/5 dark:hover:bg-[#21262d]">
                                Khóa học của tôi
                            </a>
                        @endif
                        <a href="{{ auth()->check() && !Auth::user()->isAdmin() ? route('dashboard') : route('login') }}" class="block px-3 py-2 rounded-lg text-sm text-[#1D1D1F] dark:text-[#f0f6fc] hover:bg-black/5 dark:hover:bg-[#21262d]">Danh sách yêu thích</a>
                        <a href="{{ url('/courses') }}" class="block px-3 py-2 rounded-lg text-sm text-[#1D1D1F] dark:text-[#f0f6fc] hover:bg-black/5 dark:hover:bg-[#21262d]">Bài thi & Đánh giá</a>
                    </div>
                </div>
                @guest
                    <div class="pt-3">
                        <a href="{{ route('login') }}" class="block w-full text-center py-2.5 text-sm font-semibold rounded-2xl bg-[#019D91] text-white shadow-sm">
                            Đăng nhập / Đăng ký
                        </a>
                    </div>
                @endguest
            </div>
        </div>

    </header>

    {{-- Animate UI Global Toaster --}}
    @include('partials.toaster')

    <!-- Main Content Area -->
    <main class="flex-1">
        @yield('content')
    </main>

    @if (request()->routeIs('home'))
        @guest
            @include('partials.cookie-consent')
        @endguest
    @endif

    @include('partials.back-to-top')

    <!-- Footer (Theme tối chuẩn LottieFiles - Rộng rãi, chữ to rõ ràng, không ôm sát khung) -->
    <footer class="bg-[#0b0c0e] text-[#8e8e93] {{ request()->routeIs('home') ? 'mt-0' : 'mt-12' }} select-none">
        <div class="w-full max-w-screen-2xl mx-auto px-6 sm:px-10 lg:px-14 pt-8 pb-4">
            <!-- Multi-Column Navigation (Dàn rộng sang 2 bên, thoáng đãng ở giữa) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-10 lg:gap-16 xl:gap-24 pb-8 border-b border-[#1f2228]/80">
                <div>
                    <h3 class="font-bold text-white text-base sm:text-[17px] mb-5 tracking-tight">Khám phá khóa học</h3>
                    <ul class="space-y-3.5 sm:space-y-4 text-[14px] sm:text-[15px] leading-relaxed">
                        <li><a href="{{ route('courses.index') }}" class="text-neutral-400 hover:text-white transition-colors duration-150">Tất cả khóa học</a></li>
                        <li><a href="{{ route('courses.index', ['price' => 'free']) }}" class="text-neutral-400 hover:text-white transition-colors duration-150">Khóa học miễn phí</a></li>
                        <li><a href="{{ route('courses.index', ['sort' => 'popular']) }}" class="text-neutral-400 hover:text-white transition-colors duration-150">Khóa học phổ biến</a></li>
                        <li><a href="{{ route('courses.index', ['sort' => 'rating']) }}" class="text-neutral-400 hover:text-white transition-colors duration-150">Đánh giá cao nhất</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-white text-base sm:text-[17px] mb-5 tracking-tight">Tài khoản & Học tập</h3>
                    <ul class="space-y-3.5 sm:space-y-4 text-[14px] sm:text-[15px] leading-relaxed">
                        <li><a href="{{ route('login') }}" class="text-neutral-400 hover:text-white transition-colors duration-150">Đăng nhập tài khoản</a></li>
                        <li><a href="{{ route('register') }}" class="text-neutral-400 hover:text-white transition-colors duration-150">Đăng ký thành viên</a></li>
                        @if (Route::has('my-courses') && (!auth()->check() || !Auth::user()->isAdmin()))
                            <li><a href="{{ route('my-courses') }}" class="text-neutral-400 hover:text-white transition-colors duration-150">Khóa học của tôi</a></li>
                        @endif
                        <li><a href="{{ route('profile.edit') }}" class="text-neutral-400 hover:text-white transition-colors duration-150">Quản lý hồ sơ</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-white text-base sm:text-[17px] mb-5 tracking-tight">Dành cho Giảng viên</h3>
                    <ul class="space-y-3.5 sm:space-y-4 text-[14px] sm:text-[15px] leading-relaxed">
                        <li><a href="{{ route('instructor.dashboard') }}" class="text-neutral-400 hover:text-white transition-colors duration-150">Studio giảng dạy</a></li>
                        <li><a href="{{ route('instructor.courses.create') }}" class="text-neutral-400 hover:text-white transition-colors duration-150">Tạo khóa học mới</a></li>
                        <li><a href="#" class="text-neutral-400 hover:text-white transition-colors duration-150">Quy chuẩn giảng dạy</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-white text-base sm:text-[17px] mb-5 tracking-tight">Về EduLearn</h3>
                    <ul class="space-y-3.5 sm:space-y-4 text-[14px] sm:text-[15px] leading-relaxed">
                        <li><a href="{{ route('support.index') }}" class="text-neutral-400 hover:text-white transition-colors duration-150">Trung tâm hỗ trợ</a></li>
                        <li><a href="{{ route('support.feedback') }}" class="text-neutral-400 hover:text-white transition-colors duration-150">Báo cáo sự cố & góp ý</a></li>
                        <li><a href="#" class="text-neutral-400 hover:text-white transition-colors duration-150">Giới thiệu nền tảng</a></li>
                        <li><a href="#" class="text-neutral-400 hover:text-white transition-colors duration-150">Điều khoản dịch vụ</a></li>
                        <li><a href="#" class="text-neutral-400 hover:text-white transition-colors duration-150">Chính sách bảo mật</a></li>
                        <li><a href="mailto:support@edulearn.vn" class="text-neutral-400 hover:text-white transition-colors duration-150">Liên hệ hỗ trợ</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Line (Khớp 100% mẫu LottieFiles: Cân đối 2 bên, icon trên - chữ dưới) -->
            <div class="pt-8 pb-4 flex flex-col sm:flex-row items-center sm:items-end justify-between gap-6 text-[14px]">
                <!-- Left: Logo (trên) & Bản quyền (dưới) -->
                <div class="flex flex-col items-center sm:items-start gap-2.5">
                    <a href="{{ url('/') }}" class="flex items-center space-x-2.5 text-white font-bold text-lg sm:text-xl tracking-tight">
                        <svg class="w-6 h-6 text-[#00ddb3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        <span>EduLearn</span>
                    </a>
                    <div class="flex items-center space-x-3 text-xs sm:text-sm text-neutral-400">
                        <span>&copy; {{ date('Y') }} EduLearn Inc.</span>
                        <span class="text-neutral-600">•</span>
                        <a href="#" class="hover:text-white transition-colors">Bảo mật</a>
                        <a href="#" class="hover:text-white transition-colors">Điều khoản</a>
                    </div>
                </div>

                <!-- Right: App Store & Google Play Badges + Social Icons (trên) & Made with ❤️ from Vietnam (dưới) -->
                <div class="flex flex-col items-center lg:items-end gap-3">
                    <div class="flex flex-wrap items-center justify-center lg:justify-end gap-4 sm:gap-6 text-white">
                        <!-- App Store & Google Play Badges (bên trái các icon mạng xã hội) -->
                        <div class="flex items-center gap-2.5">
                            <!-- Download on the App Store -->
                            <a href="#" class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-lg border border-neutral-700 hover:border-neutral-400 bg-black text-white hover:bg-[#141518] transition-all duration-150 shadow-sm group" title="Download on the App Store">
                                <svg class="w-4 h-5 fill-white shrink-0 transition-transform group-hover:scale-105" viewBox="0 0 814 1000" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76.5 0-103.7 40.8-165.9 40.8s-105.6-57-155.5-127C46.7 790.7 0 663 0 541.8c0-194.4 126.4-297.5 250.8-297.5 66.1 0 121.2 43.4 162.7 43.4 39.5 0 101.1-46 176.3-46 28.5 0 130.9 2.6 198.3 99.2zm-234-181.5c31.1-36.9 53.1-88.1 53.1-139.3 0-7.1-.6-14.3-1.9-20.1-50.6 1.9-110.8 33.7-147.1 75.8-28.5 32.4-55.1 83.6-55.1 135.5 0 7.8 1.3 15.6 1.9 18.1 3.2.6 8.4 1.3 13.6 1.3 45.4 0 102.5-30.4 135.5-71.3z"/>
                                </svg>
                                <div class="flex flex-col text-left leading-none">
                                    <span class="text-[9px] text-neutral-300 font-normal tracking-tight">Download on the</span>
                                    <span class="text-[13px] font-semibold text-white tracking-tight mt-0.5 font-sans">App Store</span>
                                </div>
                            </a>

                            <!-- GET IT ON Google Play -->
                            <a href="#" class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-lg border border-neutral-700 hover:border-neutral-400 bg-black text-white hover:bg-[#141518] transition-all duration-150 shadow-sm group" title="Get it on Google Play">
                                <svg class="w-4 h-5 shrink-0 transition-transform group-hover:scale-105" viewBox="0 0 28.5 32" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M.12 2.66a3.46 3.46 0 0 0-.12.92v24.84a3.66 3.66 0 0 0 .12.92L14 15.64Z" fill="#4285f4"/>
                                    <path d="M13.54 15.28.12 29.34a3.64 3.64 0 0 0 5.33 2.16l15.1-8.6z" fill="#ea4335"/>
                                    <path d="m27.11 12.89-6.53-3.74-7.35 6.45 7.38 7.28 6.48-3.7a3.55 3.55 0 0 0 0-6.29z" fill="#fbbc04"/>
                                    <path d="m13.64 16 6.94-6.85L5.5.51A3.72 3.72 0 0 0 3.63 0 3.64 3.64 0 0 0 .12 2.65Z" fill="#34a853"/>
                                </svg>
                                <div class="flex flex-col text-left leading-none">
                                    <span class="text-[8px] uppercase tracking-wider text-neutral-300 font-medium">GET IT ON</span>
                                    <span class="text-[13px] font-semibold text-white tracking-tight mt-0.5 font-sans">Google Play</span>
                                </div>
                            </a>
                        </div>

                        <!-- Social Media Icons -->
                        <div class="flex items-center space-x-6 text-white">
                        <!-- Facebook -->
                        <a href="https://facebook.com" target="_blank" rel="noopener" class="hover:text-[#00ddb3] transition-colors" title="Facebook">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <!-- Instagram -->
                        <a href="https://instagram.com" target="_blank" rel="noopener" class="hover:text-[#00ddb3] transition-colors" title="Instagram">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <!-- X (Twitter) -->
                        <a href="https://x.com" target="_blank" rel="noopener" class="hover:text-[#00ddb3] transition-colors" title="X (Twitter)">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <!-- YouTube -->
                        <a href="https://youtube.com" target="_blank" rel="noopener" class="hover:text-[#00ddb3] transition-colors" title="YouTube">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                        <!-- Dribbble / Web -->
                        <a href="#" class="hover:text-[#00ddb3] transition-colors" title="Global Website">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 fill-current" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm9.849 10.995c-.328-.016-2.585-.102-5.011.604-.377-.852-.785-1.705-1.229-2.537 2.684-1.127 4.984-1.023 5.315-.997.587.893.925 1.954.925 3.09v-.16zm-1.89-5.18c-.469-.026-2.454-.08-4.908.974-1.258-2.222-2.623-4.143-2.822-4.421 3.256.49 5.992 2.052 7.73 4.447zM12 2.058c.241.332 1.583 2.213 2.827 4.398-3.418 1.054-6.536 1.134-7.066 1.141 1.01-2.42 2.723-4.42 4.239-5.539zM4.095 8.924c.535-.008 3.328-.088 6.577-1.111.424.8.816 1.621 1.176 2.441-4.225 1.349-8.156 4.364-8.497 4.631-.568-1.478-.851-3.085-.851-4.757 0-.411.026-.818.077-1.218.496-.005 1.05-.008 1.518.014zm-2.037 7.025c.348-.28 3.992-3.076 8.083-4.372 1.028 2.643 1.838 5.419 2.062 6.287-3.924 1.189-7.391-.129-7.735-.27-.881-.462-1.636-1.077-2.23-1.802-.122-.164-.18-.328-.18-.443zm12.392 5.922c-.237-.905-1.033-3.64-2.05-6.241 2.285-.688 4.395-.623 4.743-.604.147 2.76-1.033 5.378-2.693 6.845zm4.013-1.658c-.378-.016-2.278-.066-4.382.571 1.545-1.42 2.624-3.791 2.756-6.301 2.072.106 3.847.785 4.195.94-.469 1.859-1.442 3.493-2.569 4.79z"/></svg>
                        </a>
                    </div>
                </div>
                    <p class="text-xs sm:text-sm text-neutral-400 flex items-center gap-1.5 font-medium whitespace-nowrap">
                        <span>Made with</span>
                        <span class="text-red-500 text-sm">❤️</span>
                        <span>from Vietnam</span>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
