<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'EduLearn')) - Nền tảng Học trực tuyến</title>

    @include('partials.theme-script')

    <!-- Preconnect Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="flex flex-col min-h-screen bg-white text-[#1D1D1F] dark:bg-[#0d1117] dark:text-[#e6edf3] antialiased selection:bg-[#019D91] selection:text-white">
    <!-- Header / Navbar (Apple Glass Strip - Expanded & Proportioned) -->
    <header class="sticky top-0 z-50 apple-glass-nav border-b border-[#E5E5EA] dark:border-[#30363d] transition-colors" x-data="{ mobileMenuOpen: false }">
        <div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-8 lg:px-12">
            <div class="flex items-center justify-between h-16 sm:h-18">
                <!-- Left: Logo & Nav Links -->
                <div class="flex items-center space-x-10">
                    <a href="{{ url('/') }}" class="flex items-center space-x-2.5 text-[#1D1D1F] dark:text-white group">
                        <svg class="w-7 h-7 text-[#1D1D1F] dark:text-white group-hover:text-[#019D91] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                        <span class="text-lg sm:text-xl font-bold tracking-tight">EduLearn</span>
                    </a>

                    <!-- Desktop Nav Links (Bigger Font Size & Spacing) -->
                    <nav class="hidden md:flex items-center space-x-8 text-sm sm:text-[15px] font-medium text-[#1D1D1F]/80 dark:text-[#e6edf3]">
                        <a href="{{ url('/') }}" class="transition-colors hover:text-[#019D91] dark:hover:text-[#02b3a5] py-2 {{ request()->routeIs('home') ? 'font-semibold text-[#1D1D1F] dark:text-white' : '' }}">
                            Trang chủ
                        </a>
                        <a href="{{ url('/courses') }}" class="transition-colors hover:text-[#019D91] dark:hover:text-[#02b3a5] py-2 {{ request()->routeIs('courses.*') ? 'font-semibold text-[#1D1D1F] dark:text-white' : '' }}">
                            Khóa học
                        </a>
                    </nav>
                </div>

                <!-- Center: Expanded Search Form -->
                <div class="hidden lg:flex items-center flex-1 max-w-lg mx-10">
                    <form action="{{ url('/courses') }}" method="GET" class="w-full relative">
                        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm kiếm các khóa học..."
                                class="w-full h-10 pl-10 pr-4 text-sm bg-black/[0.04] dark:bg-[#161b22] text-[#1D1D1F] dark:text-[#e6edf3] placeholder-[#86868B] dark:placeholder-[#7d8590] border border-transparent dark:border-[#30363d] focus:border-[#019D91]/40 dark:focus:border-[#02b3a5] rounded-full focus:ring-2 focus:ring-[#019D91]/20 transition">
                        <span class="absolute left-3.5 top-2.5 text-[#86868B] dark:text-[#7d8590]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                    </form>
                </div>

                <!-- Right: Actions (Theme Toggle + User Dropdown / Auth Buttons) -->
                <div class="flex items-center space-x-4">
                    <x-theme-toggle class="!text-[#1D1D1F] dark:!text-[#e6edf3] hover:!bg-black/5 dark:hover:!bg-[#21262d] rounded-full p-2 transition" />

                    @auth
                        @if (Route::has('my-courses'))
                            <a href="{{ route('my-courses') }}" class="hidden sm:inline-block text-sm font-medium text-[#1D1D1F]/85 dark:text-[#e6edf3] hover:text-[#019D91] dark:hover:text-[#02b3a5] transition px-2 py-1">
                                Khóa học của tôi
                            </a>
                        @endif

                        <!-- User Profile Dropdown (Chỉ hiện avatar tròn có chữ bên trong, click vào là sổ menu) -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open"
                                    class="flex items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-[#019D91] transition hover:opacity-90 active:scale-95"
                                    aria-label="Tài khoản cá nhân">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#1D1D1F] text-white dark:bg-[#21262d] dark:text-white font-semibold text-sm sm:text-base flex items-center justify-center overflow-hidden ring-2 ring-black/10 dark:ring-[#30363d] shadow-sm select-none">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                                    @endif
                                </div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" x-cloak
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-60 bg-white dark:bg-[#161b22] text-[#1D1D1F] dark:text-[#e6edf3] rounded-2xl shadow-xl border border-[#E5E5EA] dark:border-[#30363d] py-2 z-50 text-sm">

                                <div class="px-4 py-3 border-b border-[#E5E5EA] dark:border-[#30363d]">
                                    <p class="font-semibold truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-[#86868B] dark:text-[#7d8590] truncate mt-0.5">{{ Auth::user()->email }}</p>
                                    <span class="inline-block mt-2 px-2.5 py-0.5 text-[11px] font-medium rounded-full
                                        {{ Auth::user()->isAdmin() ? 'bg-neutral-900 text-white dark:bg-[#30363d] dark:text-white' : (Auth::user()->isInstructor() ? 'bg-[#019D91]/15 text-[#018076] dark:bg-[#019D91]/20 dark:text-[#02b3a5]' : 'bg-neutral-100 text-neutral-800 dark:bg-[#21262d] dark:text-[#c9d1d9]') }}">
                                        {{ Auth::user()->isAdmin() ? 'Quản trị viên' : (Auth::user()->isInstructor() ? 'Giảng viên' : 'Học viên') }}
                                    </span>
                                </div>

                                @unless(Auth::user()->isAdmin() || Auth::user()->isInstructor())
                                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2.5 hover:bg-[#F5F5F7] dark:hover:bg-[#21262d] transition">
                                        Bảng điều khiển
                                    </a>
                                @endunless

                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 hover:bg-[#F5F5F7] dark:hover:bg-[#21262d] transition font-medium text-[#019D91] dark:text-[#02b3a5]">
                                        Trang Quản trị Admin
                                    </a>
                                @endif

                                @if(Auth::user()->isInstructor() || Auth::user()->isAdmin())
                                    <a href="{{ route('instructor.dashboard') }}" class="flex items-center px-4 py-2.5 hover:bg-[#F5F5F7] dark:hover:bg-[#21262d] transition font-medium text-[#019D91] dark:text-[#02b3a5]">
                                        Studio Giảng viên
                                    </a>
                                @endif

                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 hover:bg-[#F5F5F7] dark:hover:bg-[#21262d] transition">
                                    Hồ sơ cá nhân
                                </a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center px-4 py-2.5 text-rose-600 dark:text-rose-400 hover:bg-[#F5F5F7] dark:hover:bg-[#21262d] transition border-t border-[#E5E5EA] dark:border-[#30363d]">
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Single Uiverse Interactive 'Đăng nhập' Button (with embedded sign up) -->
                        <a href="{{ route('login') }}"
                           class="relative inline-flex items-center justify-center h-11 w-36 rounded-2xl bg-white dark:bg-[#161b22] border border-[#E5E5EA] dark:border-[#30363d] text-sm font-semibold text-[#1D1D1F] dark:text-[#f0f6fc] overflow-hidden group shadow-sm transition-all hover:shadow hover:border-[#019D91]/40 active:scale-[0.98]">
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
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-[#1D1D1F] dark:text-white p-1.5 rounded-lg hover:bg-black/5 dark:hover:bg-[#21262d]" aria-label="Mở menu">
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
                           class="w-full h-10 text-sm bg-black/[0.04] dark:bg-[#161b22] text-[#1D1D1F] dark:text-[#e6edf3] placeholder-[#86868B] dark:placeholder-[#7d8590] border border-transparent dark:border-[#30363d] rounded-2xl px-4 focus:ring-1 focus:ring-[#019D91]">
                </form>
                <a href="{{ url('/') }}" class="block px-3 py-2 rounded-xl text-base font-medium hover:bg-black/5 dark:hover:bg-[#21262d]">Trang chủ</a>
                <a href="{{ url('/courses') }}" class="block px-3 py-2 rounded-xl text-base font-medium hover:bg-black/5 dark:hover:bg-[#21262d]">Khóa học</a>
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

    <!-- Flash Messages -->
    @if(session('success') || session('error'))
    <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 mt-4">
        @if(session('success'))
            <div class="px-5 py-3 rounded-full bg-white dark:bg-neutral-900 border border-[#D2D2D7]/60 dark:border-neutral-800 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-2.5 text-emerald-600 dark:text-emerald-400">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="text-[#1D1D1F] dark:text-white font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="px-5 py-3 rounded-full bg-white dark:bg-neutral-900 border border-rose-200 dark:border-rose-900/50 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-2.5 text-rose-600 dark:text-rose-400">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-rose-700 dark:text-rose-300 font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif
    </div>
    @endif

    <!-- Main Content Area -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-[#0d1117] text-[#86868B] dark:text-[#7d8590] border-t border-[#E5E5EA] dark:border-[#30363d] text-xs sm:text-[13px] mt-16">
        <div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-8 lg:px-12 py-10">
            <!-- Multi-Column Navigation -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-8 pb-10">
                <div>
                    <h3 class="font-semibold text-[#1D1D1F] dark:text-[#e6edf3] text-sm mb-3.5">Khám phá khóa học</h3>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('courses.index') }}" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Tất cả khóa học</a></li>
                        <li><a href="{{ route('courses.index', ['price' => 'free']) }}" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Khóa học miễn phí</a></li>
                        <li><a href="{{ route('courses.index', ['sort' => 'popular']) }}" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Khóa học phổ biến</a></li>
                        <li><a href="{{ route('courses.index', ['sort' => 'rating']) }}" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Đánh giá cao nhất</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-[#1D1D1F] dark:text-[#e6edf3] text-sm mb-3.5">Tài khoản & Học tập</h3>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('login') }}" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Đăng nhập tài khoản</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Đăng ký thành viên</a></li>
                        @if (Route::has('my-courses'))
                            <li><a href="{{ route('my-courses') }}" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Khóa học của tôi</a></li>
                        @endif
                        <li><a href="{{ route('profile.edit') }}" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Quản lý hồ sơ</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-[#1D1D1F] dark:text-[#e6edf3] text-sm mb-3.5">Dành cho Giảng viên</h3>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('instructor.dashboard') }}" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Studio giảng dạy</a></li>
                        <li><a href="{{ route('instructor.courses.create') }}" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Tạo khóa học mới</a></li>
                        <li><a href="#" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Quy chuẩn giảng dạy</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-[#1D1D1F] dark:text-[#e6edf3] text-sm mb-3.5">Về EduLearn</h3>
                    <ul class="space-y-2.5">
                        <li><a href="#" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Giới thiệu nền tảng</a></li>
                        <li><a href="#" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Điều khoản dịch vụ</a></li>
                        <li><a href="#" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Chính sách bảo mật</a></li>
                        <li><a href="mailto:support@edulearn.vn" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Liên hệ hỗ trợ</a></li>
                    </ul>
                </div>
            </div>

            <!-- Copyright and Region Line -->
            <div class="pt-8 border-t border-[#E5E5EA] dark:border-[#30363d] flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
                <p>&copy; {{ date('Y') }} EduLearn Inc. Bản quyền thuộc về dự án e-learning.</p>
                <div class="flex items-center space-x-6">
                    <a href="#" class="hover:underline">Bảo mật</a>
                    <a href="#" class="hover:underline">Điều khoản</a>
                    <a href="#" class="hover:underline">Sơ đồ trang</a>
                    <span class="text-[#1D1D1F] dark:text-white font-medium">Việt Nam</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
