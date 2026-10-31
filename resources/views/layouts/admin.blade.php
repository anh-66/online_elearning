<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Quản Trị Hệ Thống') - Admin Control Panel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full bg-white font-sans antialiased text-gray-800" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex">
        <!-- Sidebar Mobile Backdrop -->
        <div x-show="sidebarOpen" x-cloak 
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden transition-opacity"></div>

        <!-- Admin Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-950 text-slate-300 flex flex-col transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-0 shadow-2xl">
            <!-- Brand -->
            <div class="h-16 flex items-center justify-between px-6 bg-slate-900/90 border-b border-slate-800/80">
                <a href="{{ url('/admin/dashboard') }}" class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-rose-600 flex items-center justify-center text-white font-black shadow-md shadow-rose-600/30">
                        AD
                    </div>
                    <div>
                        <span class="text-base font-bold text-white tracking-wide block leading-tight">Admin System</span>
                        <span class="text-[10px] text-rose-400 font-semibold tracking-wider uppercase">Quản Trị Toàn Quyền</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Nav Items -->
            <nav class="flex-1 px-4 py-5 space-y-1 overflow-y-auto text-sm font-medium">
                <!-- Dashboard -->
                <a href="{{ url('/admin/dashboard') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->is('admin/dashboard') ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span>Thống kê & Biểu đồ</span>
                </a>

                <div class="pt-3 pb-1">
                    <span class="px-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Nghiệp vụ cốt lõi</span>
                </div>

                <!-- Người dùng -->
                <a href="{{ url('/admin/users') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->is('admin/users*') ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>Quản lý Người dùng</span>
                </a>

                <!-- Khóa học & Duyệt -->
                <a href="{{ url('/admin/courses') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->is('admin/courses*') ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Quản lý & Duyệt Khóa học</span>
                </a>

                <!-- Danh mục -->
                <a href="{{ url('/admin/categories') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->is('admin/categories*') ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    <span>Quản lý Danh mục</span>
                </a>

                <!-- Ghi danh -->
                <a href="{{ url('/admin/enrollments') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->is('admin/enrollments*') ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <span>Quản lý Ghi danh</span>
                </a>

                <!-- Đánh giá & Bình luận -->
                <a href="{{ url('/admin/reviews') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->is('admin/reviews*') ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    <span>Kiểm duyệt Đánh giá</span>
                </a>

                <div class="pt-3 pb-1">
                    <span class="px-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Cấu hình Trang</span>
                </div>

                <!-- Banners -->
                <a href="{{ url('/admin/banners') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->is('admin/banners*') ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Quản lý Banners</span>
                </a>

                <!-- Notifications -->
                <a href="{{ url('/admin/notifications') }}" 
                   class="flex items-center px-3.5 py-2.5 rounded-xl transition {{ request()->is('admin/notifications*') ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    <span>Quản lý Thông báo</span>
                </a>

                <!-- Quay về trang chủ -->
                <a href="{{ url('/') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-slate-400 hover:bg-slate-800/60 hover:text-white transition mt-4">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Về trang chủ</span>
                </a>
            </nav>

            <!-- Bottom User -->
            <div class="p-4 border-t border-slate-800/80 bg-slate-900/60">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg bg-rose-600 text-white font-bold flex items-center justify-center shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 1)) }}
                    </div>
                    <div class="truncate flex-1">
                        <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                        <span class="text-[10px] text-rose-400 font-semibold uppercase">Super Admin</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Right Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Bar -->
            <header class="h-16 bg-black text-white border-b border-gray-800 flex items-center justify-between px-4 sm:px-6 lg:px-8 shadow-sm">
                <div class="flex items-center space-x-3">
                    <button @click="sidebarOpen = true" class="lg:hidden text-gray-300 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h1 class="text-lg font-bold text-white truncate">
                        @yield('page_title', 'Bảng Quản Trị Hệ Thống')
                    </h1>
                </div>

                <div class="flex items-center space-x-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-rose-600 hover:text-rose-700 transition">Đăng xuất</button>
                    </form>
                </div>
            </header>

            <!-- Main Content Container -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <!-- Alerts -->
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
                        <p class="font-semibold mb-1">Vui lòng kiểm tra lại dữ liệu:</p>
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
