<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Phòng học trực tuyến') - {{ config('app.name', 'EduLearn') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full flex flex-col font-sans antialiased text-gray-100 overflow-hidden" x-data="{ sidebarOpen: true }">
    <!-- Top Learning Navigation Bar -->
    <header class="h-14 bg-slate-950 border-b border-slate-800 flex items-center justify-between px-4 sm:px-6 shrink-0 z-30">
        <!-- Left: Back to My Courses & Course Title -->
        <div class="flex items-center space-x-4 truncate">
            <a href="{{ url('/my-courses') }}" class="flex items-center space-x-1.5 text-xs sm:text-sm font-medium text-slate-400 hover:text-white transition shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span class="hidden sm:inline">Khóa học của tôi</span>
            </a>

            <div class="h-4 w-px bg-slate-800 hidden sm:block shrink-0"></div>

            <h1 class="text-xs sm:text-sm font-semibold text-white truncate max-w-xs md:max-w-md lg:max-w-xl">
                @yield('course_title', 'Tên khóa học')
            </h1>
        </div>

        <!-- Right: Progress Indicator & Sidebar Toggle -->
        <div class="flex items-center space-x-4 shrink-0">
            <!-- Progress Bar Info -->
            <div class="hidden md:flex items-center space-x-3 text-xs">
                <div class="w-32 bg-slate-800 rounded-full h-2 overflow-hidden border border-slate-700">
                    <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full transition-all duration-300" 
                         style="width: @yield('progress_percent', '0')%;"></div>
                </div>
                <span class="font-bold text-emerald-400">@yield('progress_percent', '0')%</span>
            </div>

            <!-- Toggle Course Curriculum Sidebar -->
            <button @click="sidebarOpen = !sidebarOpen" 
                    class="flex items-center space-x-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition border border-slate-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                <span class="hidden sm:inline" x-text="sidebarOpen ? 'Đóng mục lục' : 'Mở mục lục'"></span>
            </button>
        </div>
    </header>

    <!-- Main Learning Layout Container (2 Columns) -->
    <div class="flex-1 flex overflow-hidden relative">
        <!-- Main Content Area: Video Player, Lesson Content, Q&A -->
        <main class="flex-1 overflow-y-auto bg-slate-900 flex flex-col">
            <!-- Alerts Banner -->
            <div class="p-4 sm:p-6 max-w-5xl mx-auto w-full pb-0">
                @if(session('success'))
                    <div class="p-3.5 rounded-xl bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-sm flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-3.5 rounded-xl bg-rose-950/80 border border-rose-800 text-rose-300 text-sm flex items-center space-x-2">
                        <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
            </div>

            <!-- Page / Video Content -->
            <div class="flex-1 p-4 sm:p-6 lg:p-8 max-w-5xl mx-auto w-full">
                @yield('content')
            </div>
        </main>

        <!-- Right Sidebar: Curriculum / List of Chapters & Lessons & Quizzes -->
        <aside x-show="sidebarOpen" x-cloak
               x-transition:enter="transition ease-out duration-200"
               x-transition:enter-start="translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-150"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="translate-x-full"
               class="w-80 lg:w-96 bg-slate-950 border-l border-slate-800 flex flex-col shrink-0 z-20">
            <!-- Curriculum Header -->
            <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center space-x-2">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>Nội dung khóa học</span>
                </h2>
                <button @click="sidebarOpen = false" class="text-slate-400 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Curriculum List Container -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-800/80 text-sm">
                @yield('player_sidebar')
            </div>
        </aside>
    </div>

    @stack('scripts')
</body>
</html>
