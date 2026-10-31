<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'E-Learning')) - Nền tảng Học trực tuyến</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="flex flex-col min-h-screen font-sans bg-white text-gray-800 antialiased">
    <!-- Header / Navbar -->
    <header class="sticky top-0 z-50 bg-black text-white backdrop-blur border-b border-gray-800 shadow-sm" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-8">
                    <a href="{{ url('/') }}" class="flex items-center space-x-2">
                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-black shadow-md shadow-black/30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-white">EduLearn</span>
                    </a>

                    <!-- Desktop Nav links -->
                    <nav class="hidden md:flex items-center space-x-6 text-sm font-semibold text-white">
                        <a href="{{ url('/') }}" class="font-bold text-white hover:text-gray-200 transition">Trang chủ</a>
                        <a href="{{ url('/courses') }}" class="font-bold text-white hover:text-gray-200 transition">Khóa học</a>
                    </nav>
                </div>

                <!-- Search Bar -->
                <div class="hidden lg:flex items-center flex-1 max-w-md mx-8">
                    <form action="{{ url('/courses') }}" method="GET" class="w-full relative">
                        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm kiếm khóa học, kỹ năng..." 
                               class="w-full pl-10 pr-4 py-2 text-sm bg-gray-800 text-white placeholder:text-gray-400 border-none rounded-full focus:bg-gray-700 focus:ring-2 focus:ring-white transition">
                        <span class="absolute left-3 top-2.5 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                    </form>
                </div>

                <!-- Right Actions: Auth / User Menu -->
                <div class="flex items-center space-x-4">
                    @auth
                        <!-- Khóa học của tôi -->
                        <a href="{{ url('/my-courses') }}" class="hidden sm:inline-flex items-center space-x-1.5 text-sm font-bold text-white hover:text-gray-200 transition">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span>Khóa học của tôi</span>
                        </a>

                        <!-- User Dropdown (Alpine.js) -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 font-semibold flex items-center justify-center border-2 border-indigo-200 overflow-hidden">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    @endif
                                </div>
                                <span class="hidden md:inline text-sm font-bold text-white max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5 z-50 text-sm">
                                
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-medium rounded-full 
                                        {{ Auth::user()->isAdmin() ? 'bg-rose-100 text-rose-700' : (Auth::user()->isInstructor() ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                                        {{ strtoupper(Auth::user()->role) }}
                                    </span>
                                </div>

                                @if(Auth::user()->isAdmin())
                                    <a href="{{ url('/admin/dashboard') }}" class="flex items-center px-4 py-2 text-rose-600 hover:bg-rose-50 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        Trang Quản trị Admin
                                    </a>
                                @endif

                                @if(Auth::user()->isInstructor() || Auth::user()->isAdmin())
                                    <a href="{{ url('/instructor/dashboard') }}" class="flex items-center px-4 py-2 text-indigo-600 hover:bg-indigo-50 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                                        Studio Giảng viên
                                    </a>
                                @endif

                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Hồ sơ cá nhân
                                </a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-red-600 hover:bg-red-50 transition border-t border-gray-100">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Guest Auth buttons -->
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-300 hover:text-white transition px-3 py-2">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="text-sm font-medium text-black bg-white hover:bg-gray-200 px-4 py-2 rounded-lg shadow-sm transition">Đăng ký</a>
                    @endauth

                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-300 hover:text-white focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Search & Menu -->
            <div x-show="mobileMenuOpen" x-cloak class="md:hidden border-t border-gray-800 py-3 space-y-2">
                <form action="{{ url('/courses') }}" method="GET" class="mb-3">
                    <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm kiếm khóa học..." 
                           class="w-full text-sm bg-gray-800 text-white border-none rounded-lg px-3 py-2">
                </form>
                <a href="{{ url('/') }}" class="block px-3 py-2 rounded-md text-base font-bold text-white hover:bg-gray-800">Trang chủ</a>
                <a href="{{ url('/courses') }}" class="block px-3 py-2 rounded-md text-base font-bold text-white hover:bg-gray-800">Danh sách Khóa học</a>
                @auth
                    <a href="{{ url('/my-courses') }}" class="block px-3 py-2 rounded-md text-base font-bold text-white hover:bg-gray-800">Khóa học của tôi</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Flash Messages Banner -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-black text-gray-400 mt-20 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Col 1: Brand Info -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold">E</div>
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
                        <li><a href="{{ url('/courses') }}" class="hover:text-white transition">Tất cả khóa học</a></li>
                        <li><a href="{{ url('/courses?filter=free') }}" class="hover:text-white transition">Khóa học miễn phí</a></li>
                        <li><a href="{{ url('/courses?filter=popular') }}" class="hover:text-white transition">Khóa học nổi bật</a></li>
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

            <div class="mt-8 pt-8 border-t border-gray-800 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} EduLearn Platform. All rights reserved. Thiết kế cho môn Học phần Team Project.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
