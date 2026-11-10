<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-neutral-50 dark:bg-neutral-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('partials.theme-script')

    <title>@yield('title', 'Studio Giảng viên') - {{ config('app.name', 'EduLearn') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700|figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full bg-neutral-50 font-sans antialiased text-gray-800 dark:bg-neutral-950 dark:text-neutral-200" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex">
        <!-- Sidebar Mobile Backdrop -->
        <div x-show="sidebarOpen" x-cloak 
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden transition-opacity"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-white text-neutral-600 border-r border-neutral-200 dark:bg-neutral-900 dark:text-neutral-300 dark:border-neutral-800 flex flex-col transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-0 shadow-xl">
            <!-- Sidebar Header / Logo -->
            <div class="h-16 flex items-center justify-between px-6 border-b border-neutral-200 dark:border-neutral-800">
                <a href="{{ route('instructor.dashboard') }}" class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-brand-700 flex items-center justify-center text-white font-bold shadow-md shadow-brand-600/20">
                        IN
                    </div>
                    <div>
                        <span class="text-base font-bold text-neutral-900 dark:text-white tracking-wide block leading-tight">Studio Giảng Viên</span>
                        <span class="text-[10px] text-neutral-500 dark:text-neutral-400 font-medium tracking-wider uppercase">Instructor Portal</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-neutral-400 hover:text-neutral-900 dark:hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto text-sm font-medium">
                <!-- Dashboard -->
                <a href="{{ route('instructor.dashboard') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->is('instructor/dashboard') ? 'bg-brand-700 text-white shadow-md shadow-brand-700/30' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span>Tổng quan (Dashboard)</span>
                </a>

                <!-- Khóa học của tôi -->
                <a href="{{ route('instructor.courses.index') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->is('instructor/courses*', 'instructor/chapters*', 'instructor/lessons*', 'instructor/quizzes*', 'instructor/questions*') && ! request()->is('instructor/courses/create') ? 'bg-brand-700 text-white shadow-md shadow-brand-700/30' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Khóa học của tôi</span>
                </a>

                <!-- Tạo khóa học mới -->
                <a href="{{ route('instructor.courses.create') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->is('instructor/courses/create') ? 'bg-brand-700 text-white shadow-md shadow-brand-700/30' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Tạo khóa học mới</span>
                </a>

                <!-- Danh sách Học viên -->
                <a href="{{ route('instructor.students.index') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->is('instructor/students*') ? 'bg-brand-700 text-white shadow-md shadow-brand-700/30' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>Quản lý Học viên</span>
                </a>

                <!-- Thu nhập & Rút tiền (70/30) -->
                <a href="{{ route('instructor.payouts.index') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->is('instructor/payouts*') ? 'bg-brand-700 text-white shadow-md shadow-brand-700/30' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Thu nhập & Rút tiền</span>
                </a>

                <div class="pt-4 pb-2">
                    <span class="px-3 text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Học tập</span>
                </div>

                <!-- Khóa học đang học (với tư cách học viên) -->
                <a href="{{ route('dashboard') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white transition">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                    <span>Khóa học của tôi (Học viên)</span>
                </a>

                <div class="pt-3 pb-2">
                    <span class="px-3 text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider">Hệ thống</span>
                </div>

                <!-- Quay về trang chủ -->
                <a href="{{ url('/') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-neutral-500 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-white transition">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Về trang chủ Public</span>
                </a>
            </nav>

            <!-- Bottom User Card -->
            <div class="p-4 border-t border-neutral-200 dark:border-neutral-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 truncate">
                        <div class="w-9 h-9 rounded-lg bg-black text-white font-bold flex items-center justify-center shrink-0 overflow-hidden">
                            @if(Auth::user()->avatar_url)
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')">
                                <span class="hidden flex items-center justify-center w-full h-full">{{ Auth::user()->initialLetter() }}</span>
                            @else
                                <span>{{ Auth::user()->initialLetter() }}</span>
                            @endif
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-semibold text-neutral-900 dark:text-white truncate">{{ Auth::user()->name ?? 'Giảng viên' }}</p>
                            <p class="text-[11px] text-neutral-500 dark:text-neutral-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Right Main Column -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <header class="h-16 bg-white border-b border-neutral-200 dark:bg-neutral-900 dark:border-neutral-800 flex items-center justify-between px-4 sm:px-6 lg:px-8 shadow-sm">
                <div class="flex items-center space-x-3">
                    <button @click="sidebarOpen = true" class="lg:hidden text-neutral-500 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-white" aria-label="Mở menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h1 class="text-lg font-bold text-neutral-900 dark:text-white truncate uppercase tracking-wide">
                        @yield('page_title', 'Studio Giảng viên')
                    </h1>
                </div>

                <div class="flex items-center space-x-3 sm:space-x-4">
                    @include('partials.notification-bell')
                    <x-theme-toggle />
                    <a href="{{ route('profile.edit') }}" class="text-sm font-medium text-neutral-600 hover:text-brand-700 dark:text-neutral-300 dark:hover:text-brand-400 transition">Hồ sơ</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition">Đăng xuất</button>
                    </form>
                </div>
            </header>

            <!-- Main Content Area -->
            <main data-back-to-top-scroll class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <!-- Flash Alerts -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-sm flex items-center space-x-2 shadow-sm">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    @include('partials.error-alert', ['message' => session('error')])
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 text-sm shadow-sm">
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
</body>
</html>
