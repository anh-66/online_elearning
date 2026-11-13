<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Phòng học trực tuyến') - {{ config('app.name', 'EduLearn') }}</title>

    @include('partials.theme-script')

    <!-- Preconnect Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        body, button, input, textarea, select {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
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
<body x-data="{ sidebarOpen: true }" class="min-h-screen flex flex-col bg-[#fafafa] text-[#1a1a1a] dark:bg-[#0d1117] dark:text-[#e6edf3] antialiased selection:bg-black selection:text-white dark:selection:bg-white dark:selection:text-black transition-colors">
    <!-- Header (Matching Minimal Modern LMS Header) -->
    <header class="sticky top-0 z-50 bg-white dark:bg-[#161b22] border-b border-gray-100 dark:border-neutral-800 px-6 py-4 flex items-center justify-between transition-colors shadow-2xs">
        <div class="flex items-center gap-6 min-w-0">
            <a href="@yield('back_url', url('/my-courses'))" id="nav-back-link" class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors shrink-0">
                <iconify-icon icon="lucide:arrow-left" class="text-lg"></iconify-icon>
                <span>Quay lại khóa học</span>
            </a>
            <div class="h-4 w-px bg-gray-200 dark:bg-neutral-800 shrink-0"></div>
            <h1 class="text-[15px] font-medium tracking-tight text-gray-900 dark:text-white truncate">
                @yield('course_title', 'Tên khóa học')
            </h1>
        </div>
        
        <div class="flex items-center gap-4 sm:gap-6 shrink-0">
            <!-- Progress Bar -->
            <div class="flex items-center gap-3">
                <div class="w-24 sm:w-32 h-1.5 bg-gray-100 dark:bg-neutral-800 rounded-full overflow-hidden">
                    <div class="h-full bg-black dark:bg-white rounded-full transition-all duration-500" style="width: @yield('progress_percent', '0')%"></div>
                </div>
                <span class="text-[13px] font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">Tiến độ: @yield('progress_percent', '0')%</span>
            </div>

            <!-- Nút bật/tắt mục lục (Phóng to màn hình) -->
            <button type="button"
                    @click="sidebarOpen = !sidebarOpen"
                    class="p-2 rounded-lg text-gray-400 hover:text-black dark:hover:text-white hover:bg-gray-100 dark:hover:bg-neutral-800 transition-colors cursor-pointer"
                    :title="sidebarOpen ? 'Thu gọn mục lục (Phóng to khung học)' : 'Mở mục lục bài học'">
                <iconify-icon :icon="sidebarOpen ? 'lucide:panel-right-close' : 'lucide:panel-right-open'" class="text-xl block"></iconify-icon>
            </button>

            <!-- Theme Toggle & User Avatar -->
            <div class="flex items-center gap-3 sm:gap-4">
                <x-theme-toggle class="!text-gray-400 hover:!text-black dark:!text-gray-400 dark:hover:!text-white transition-colors p-1" />
                <div class="w-8 h-8 rounded-full bg-gray-900 text-white dark:bg-white dark:text-black flex items-center justify-center text-xs font-bold overflow-hidden shadow-2xs">
                    @auth
                        @if(Auth::user()->avatar_url)
                            <img src="{{ Auth::user()->avatar_url }}" referrerpolicy="no-referrer" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                        @else
                            <span>{{ Auth::user()->initialLetter() }}</span>
                        @endif
                    @else
                        <span>U</span>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col">
        @yield('content')
    </main>

    @include('partials.toaster')
    @include('partials.back-to-top')
    @stack('scripts')
</body>
</html>
