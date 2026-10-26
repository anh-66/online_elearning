<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Studio Giảng viên') - {{ config('app.name', 'EduLearn') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full font-sans antialiased text-gray-800" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex">
        <!-- Sidebar Mobile Backdrop -->
        <div x-show="sidebarOpen" x-cloak 
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden transition-opacity"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 flex flex-col transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-0 shadow-xl">
            <!-- Sidebar Header / Logo -->
            <div class="h-16 flex items-center justify-between px-6 bg-slate-950/60 border-b border-slate-800">
                <a href="{{ url('/instructor/dashboard') }}" class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold shadow-md shadow-indigo-500/20">
                        IN
                    </div>
                    <div>
                        <span class="text-base font-bold text-white tracking-wide block leading-tight">Studio Giảng Viên</span>
                        <span class="text-[10px] text-slate-400 font-medium tracking-wider uppercase">Instructor Portal</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto text-sm font-medium">
                <!-- Dashboard -->
                <a href="{{ url('/instructor/dashboard') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->is('instructor/dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span>Tổng quan (Dashboard)</span>
                </a>

                <!-- Khóa học của tôi -->
                <a href="{{ url('/instructor/courses') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->is('instructor/courses') && !request()->is('instructor/courses/create') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Khóa học của tôi</span>
                </a>

                <!-- Tạo khóa học mới -->
                <a href="{{ url('/instructor/courses/create') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->is('instructor/courses/create') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Tạo khóa học mới</span>
                </a>

                <!-- Danh sách Học viên -->
                <a href="{{ url('/instructor/students') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->is('instructor/students*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>Quản lý Học viên</span>
                </a>

                <div class="pt-4 pb-2">
                    <span class="px-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Hệ thống</span>
                </div>

                <!-- Quay về trang chủ -->
                <a href="{{ url('/') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Về trang chủ Public</span>
                </a>
            </nav>

            <!-- Bottom User Card -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 truncate">
                        <div class="w-9 h-9 rounded-lg bg-indigo-600 text-white font-bold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr(Auth::user()->name ?? 'GV', 0, 1)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? 'Giảng viên' }}</p>
                            <p class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Right Main Column -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 shadow-sm">
                <div class="flex items-center space-x-3">
                    <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h1 class="text-lg font-bold text-gray-900 truncate">
                        @yield('page_title', 'Studio Giảng viên')
                    </h1>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('profile.edit') }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition">Hồ sơ</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700 transition">Đăng xuất</button>
                    </form>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <!-- Flash Alerts -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center space-x-2 shadow-sm">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center space-x-2 shadow-sm">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
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
</body>
</html>
