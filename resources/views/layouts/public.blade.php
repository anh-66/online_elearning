<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'E-Learning')) - Nền tảng Học trực tuyến</title>

    @include('partials.theme-script')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700|figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="flex flex-col min-h-screen font-sans bg-slate-50 text-gray-800 antialiased dark:bg-neutral-950 dark:text-neutral-200">
    <!-- Header / Navbar (thanh liên hệ + thanh điều hướng tông teal) -->
    <header class="sticky top-0 z-50 shadow-sm" x-data="{ mobileMenuOpen: false }">
        <div class="hidden sm:block bg-brand-900 text-brand-100 text-xs dark:bg-neutral-950 dark:text-neutral-400">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-8 flex items-center justify-between">
                <div class="flex items-center gap-5">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        Hotline: 1900 xxxx
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        support@edulearn.vn
                    </span>
                </div>
                <span class="hidden md:inline">Hệ thống học trực tuyến dành cho sinh viên</span>
            </div>
        </div>

        <div class="bg-brand-700 text-white dark:bg-brand-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-6">
                    <a href="{{ url('/') }}" class="flex items-center space-x-2.5">
                        <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center text-white ring-1 ring-white/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                        </div>
                        <span class="leading-tight">
                            <span class="block text-lg font-bold tracking-wide uppercase">EduLearn</span>
                            <span class="block text-[11px] text-brand-100">Học mọi lúc, mọi nơi</span>
                        </span>
                    </a>

                    <!-- Desktop Nav links -->
                    <nav class="hidden md:flex items-center gap-1 text-sm font-semibold">
                        <a href="{{ url('/') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('home') ? 'bg-white/15 text-white' : 'text-brand-50 hover:bg-white/10 hover:text-white' }}">Trang chủ</a>
                        <a href="{{ url('/courses') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('courses.*') ? 'bg-white/15 text-white' : 'text-brand-50 hover:bg-white/10 hover:text-white' }}">Khóa học</a>
                    </nav>
                </div>

                <!-- Search Bar -->
                <div class="hidden lg:flex items-center flex-1 max-w-md mx-8">
                    <form action="{{ url('/courses') }}" method="GET" class="w-full relative">
                        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm khóa học..."
                               class="w-full pl-10 pr-4 py-2 text-sm !bg-white/10 !text-white placeholder:!text-brand-100 !border-white/20 rounded-lg focus:!bg-white/15 focus:!border-white/40 focus:ring-white/20 transition">
                        <span class="absolute left-3 top-2.5 text-brand-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                    </form>
                </div>

                <!-- Right Actions: Auth / User Menu -->
                <div class="flex items-center space-x-3">
                    <x-theme-toggle class="!text-white hover:!bg-white/10" />

                    @auth
                        <!-- Khóa học của tôi (hiện khi TV3 đã khai báo route my-courses) -->
                        @if (Route::has('my-courses'))
                            <a href="{{ route('my-courses') }}" class="hidden sm:inline-flex items-center space-x-1.5 text-sm font-semibold text-brand-50 hover:text-white transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <span>Khóa học của tôi</span>
                            </a>
                        @endif

                        <!-- User Dropdown (Alpine.js) -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                                <div class="w-9 h-9 rounded-full bg-white/15 text-white font-semibold flex items-center justify-center ring-2 ring-white/30 overflow-hidden">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                                    @endif
                                </div>
                                <span class="hidden md:inline text-sm font-semibold text-white max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-brand-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 bg-white text-gray-800 dark:bg-neutral-900 rounded-xl shadow-lg border border-gray-100 dark:border-neutral-800 py-1.5 z-50 text-sm">

                                <div class="px-4 py-2 border-b border-gray-100 dark:border-neutral-800">
                                    <p class="font-semibold text-gray-900 dark:text-neutral-100 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-neutral-400 truncate">{{ Auth::user()->email }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-medium rounded-full
                                        {{ Auth::user()->isAdmin() ? 'bg-rose-100 dark:bg-rose-500/15 text-rose-700 dark:text-rose-400' : (Auth::user()->isInstructor() ? 'bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400' : 'bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400') }}">
                                        {{ Auth::user()->isAdmin() ? 'Quản trị viên' : (Auth::user()->isInstructor() ? 'Giảng viên' : 'Học viên') }}
                                    </span>
                                </div>

                                @unless(Auth::user()->isAdmin() || Auth::user()->isInstructor())
                                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-brand-700 dark:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-500/15 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                        Bảng điều khiển
                                    </a>
                                @endunless

                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/15 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        Trang Quản trị Admin
                                    </a>
                                @endif

                                @if(Auth::user()->isInstructor() || Auth::user()->isAdmin())
                                    <a href="{{ route('instructor.dashboard') }}" class="flex items-center px-4 py-2 text-brand-700 dark:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-500/15 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                        Studio Giảng viên
                                    </a>
                                @endif

                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-gray-700 dark:text-neutral-200 hover:bg-gray-50 dark:hover:bg-neutral-800 transition">
                                    <svg class="w-4 h-4 mr-2 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Hồ sơ cá nhân
                                </a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/15 transition border-t border-gray-100 dark:border-neutral-800">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Guest Auth buttons -->
                        <a href="{{ route('login') }}" class="hidden sm:inline-block text-sm font-semibold text-white bg-white/15 hover:bg-white/25 px-4 py-2 rounded-lg transition">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="text-sm font-semibold text-brand-800 bg-white hover:bg-brand-50 px-4 py-2 rounded-lg shadow-sm transition">Đăng ký</a>
                    @endauth

                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-brand-50 hover:text-white focus:outline-none" aria-label="Mở menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Search & Menu -->
            <div x-show="mobileMenuOpen" x-cloak class="md:hidden border-t border-white/15 py-3 space-y-2">
                <form action="{{ url('/courses') }}" method="GET" class="mb-3">
                    <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm kiếm khóa học..."
                           class="w-full text-sm !bg-white/10 !text-white placeholder:!text-brand-100 !border-white/20 rounded-lg px-3 py-2 focus:ring-white/20">
                </form>
                <a href="{{ url('/') }}" class="block px-3 py-2 rounded-md text-base font-semibold text-white hover:bg-white/10">Trang chủ</a>
                <a href="{{ url('/courses') }}" class="block px-3 py-2 rounded-md text-base font-semibold text-white hover:bg-white/10">Danh sách Khóa học</a>
                @auth
                    @if (Route::has('my-courses'))
                        <a href="{{ route('my-courses') }}" class="block px-3 py-2 rounded-md text-base font-semibold text-white hover:bg-white/10">Khóa học của tôi</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-semibold text-white hover:bg-white/10">Đăng nhập</a>
                @endauth
            </div>
        </div>
        </div>
    </header>

    <!-- Flash Messages Banner -->
    @if(session('success') || session('error'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('error') }}</span>
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
    <footer class="bg-brand-950 text-brand-100 mt-16 dark:bg-neutral-900 dark:text-neutral-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Col 1: Brand Info -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg bg-white/10 ring-1 ring-white/15 flex items-center justify-center text-white font-bold">E</div>
                        <span class="text-xl font-bold text-white">EduLearn</span>
                    </div>
                    <p class="text-sm leading-relaxed">
                        Nền tảng kết nối giảng viên tâm huyết và học viên đam mê kiến thức. Học trực tuyến mọi lúc, mọi nơi, nâng tầm sự nghiệp.
                    </p>
                </div>

                <!-- Col 2: Liên kết nhanh -->
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Khám phá</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('courses.index') }}" class="hover:text-white transition">Tất cả khóa học</a></li>
                        <li><a href="{{ route('courses.index', ['price' => 'free']) }}" class="hover:text-white transition">Khóa học miễn phí</a></li>
                        <li><a href="{{ route('courses.index', ['sort' => 'popular']) }}" class="hover:text-white transition">Khóa học nổi bật</a></li>
                    </ul>
                </div>

                <!-- Col 3: Hỗ trợ & Chính sách -->
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Hỗ trợ</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition">Điều khoản sử dụng</a></li>
                        <li><a href="#" class="hover:text-white transition">Chính sách bảo mật</a></li>
                        <li><a href="#" class="hover:text-white transition">Câu hỏi thường gặp (FAQ)</a></li>
                    </ul>
                </div>

                <!-- Col 4: Liên hệ -->
                <div>
                    <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Liên hệ</h3>
                    <p class="text-sm">Email: support@edulearn.vn</p>
                    <p class="text-sm mt-1">Hotline: 1900 xxxx</p>
                    <p class="text-sm mt-1">Địa chỉ: Hà Nội / TP. Hồ Chí Minh</p>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-white/10 text-center text-xs text-brand-200/70 dark:text-neutral-500">
                &copy; {{ date('Y') }} EduLearn Platform. All rights reserved. Thiết kế cho môn Học phần Team Project.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
