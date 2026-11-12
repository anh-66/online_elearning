<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Phòng học trực tuyến') - {{ config('app.name', 'EduLearn') }}</title>

    @include('partials.theme-script')

    <!-- Preconnect Fonts: Be Vietnam Pro -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        body {
            font-family: 'Be Vietnam Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #262c36;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #374151;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-[#F9FAFB] text-[#111827] dark:bg-[#0d1117] dark:text-[#e6edf3] font-sans antialiased selection:bg-[#10B981] selection:text-white transition-colors">
    <!-- Navigation Bar (Minimal Modern Learning Dashboard) -->
    <header class="sticky top-0 z-50 bg-white dark:bg-[#161b22] border-b border-gray-100 dark:border-neutral-800 transition-colors shadow-sm" data-sd-id="2">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 h-16 flex items-center justify-between" data-sd-id="3">
            <!-- Left: Back link + Course Title -->
            <div class="flex items-center gap-4 sm:gap-6 min-w-0" data-sd-id="4">
                <a href="@yield('back_url', url('/my-courses'))" id="nav-back-link" class="flex items-center text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors group shrink-0" data-sd-id="5">
                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    <span class="ml-2 text-sm font-semibold" data-sd-id="6">Quay lại khóa học</span>
                </a>
                <div class="h-4 w-[1px] bg-gray-200 dark:bg-neutral-700 shrink-0" data-sd-id="7"></div>
                <h1 class="text-sm sm:text-base font-bold tracking-tight text-gray-900 dark:text-white truncate max-w-xs md:max-w-md lg:max-w-xl" data-sd-id="8">
                    @yield('course_title', 'Tên khóa học')
                </h1>
            </div>
            
            <!-- Right: Progress + Theme toggle + User Avatar -->
            <div class="flex items-center gap-4 sm:gap-8 shrink-0" data-sd-id="9">
                <div class="flex flex-col items-end">
                    <div class="flex items-center gap-2.5 mb-1">
                        <span class="text-xs font-bold text-gray-400 dark:text-neutral-500 uppercase tracking-wider">Tiến độ học tập</span>
                        <span class="text-xs sm:text-sm font-extrabold text-[#059669] dark:text-[#34D399]">@yield('progress_text', '0%')</span>
                    </div>
                    <div class="w-36 sm:w-48 h-2 bg-gray-100 dark:bg-neutral-800 rounded-full overflow-hidden">
                        <div class="h-full bg-[#10B981] rounded-full transition-all duration-300" style="width: @yield('progress_percent', '0')%"></div>
                    </div>
                </div>
                <div class="h-8 w-[1px] bg-gray-100 dark:bg-neutral-800 shrink-0"></div>

                <!-- Theme Toggle -->
                <x-theme-toggle class="!text-gray-600 dark:!text-gray-300 hover:!text-gray-900 dark:hover:!text-white hover:!bg-black/5 dark:hover:!bg-white/10 rounded-full p-2 transition" />

                <!-- User Avatar -->
                <button id="nav-user-menu" class="flex items-center gap-2" data-sd-id="17">
                    <div class="w-9 h-9 rounded-full bg-gray-900 dark:bg-neutral-800 text-white flex items-center justify-center text-xs font-bold overflow-hidden ring-1 ring-black/5 dark:ring-white/10 shadow-sm" data-sd-id="18">
                        @auth
                            @if(Auth::user()->avatar_url)
                                <img src="{{ Auth::user()->avatar_url }}" referrerpolicy="no-referrer" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                            @else
                                <span>{{ Auth::user()->initialLetter() }}</span>
                            @endif
                        @else
                            <span>JD</span>
                        @endauth
                    </div>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col">
        @yield('content')
    </main>

    @include('partials.back-to-top')
    @stack('scripts')
</body>
</html>
