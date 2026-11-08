<!-- Apple Style Features Mega Menu Flyout (Tiện ích) -->
<div x-show="featuresFlyoutOpen"
     x-cloak
     x-transition:enter="transition ease-out duration-250"
     x-transition:enter-start="opacity-0 -translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2"
     @mouseenter="openFeaturesFlyout()"
     @mouseleave="scheduleFeaturesClose()"
     class="absolute left-0 right-0 top-full w-full bg-white dark:bg-[#0d1117] border-b border-[#E5E5EA] dark:border-[#30363d] shadow-2xl z-40 transition-colors">

    <div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-8 lg:px-12 py-8 sm:py-10">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-16">
            <!-- Column 1: Khám Phá Tiện Ích (Big Bold Typography like Apple Support) -->
            <div class="md:col-span-6 lg:col-span-5 space-y-4">
                <span class="text-xs font-semibold text-[#86868B] dark:text-[#7d8590] tracking-wider uppercase block">
                    Khám Phá Tiện Ích
                </span>
                <nav class="flex flex-col space-y-2 sm:space-y-2.5">
                    <a href="{{ auth()->check() ? route('my-courses') : route('login') }}"
                       class="text-xl sm:text-2xl font-bold text-[#1D1D1F] dark:text-white hover:text-[#019D91] dark:hover:text-[#02b3a5] transition duration-150 tracking-tight flex items-center justify-between group">
                        <span>Khóa học của tôi</span>
                        <svg class="w-5 h-5 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition text-[#019D91]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ auth()->check() ? route('my-courses') : route('login') }}"
                       class="text-xl sm:text-2xl font-bold text-[#1D1D1F] dark:text-white hover:text-[#019D91] dark:hover:text-[#02b3a5] transition duration-150 tracking-tight flex items-center justify-between group">
                        <span>Danh sách yêu thích</span>
                        <svg class="w-5 h-5 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition text-[#019D91]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ url('/courses') }}"
                       class="text-xl sm:text-2xl font-bold text-[#1D1D1F] dark:text-white hover:text-[#019D91] dark:hover:text-[#02b3a5] transition duration-150 tracking-tight flex items-center justify-between group">
                        <span>Bài thi & Đánh giá</span>
                        <svg class="w-5 h-5 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition text-[#019D91]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </nav>
            </div>

            <!-- Column 2: Quản Lý Học Tập (Medium Regular Links) -->
            <div class="md:col-span-6 lg:col-span-7 space-y-4">
                <span class="text-xs font-semibold text-[#86868B] dark:text-[#7d8590] tracking-wider uppercase block">
                    Quản Lý Học Tập
                </span>
                <ul class="space-y-3 text-sm font-medium">
                    <li>
                        <a href="{{ auth()->check() ? route('my-courses') : route('login') }}" class="text-[#1D1D1F] dark:text-[#e6edf3] hover:text-[#019D91] dark:hover:text-[#02b3a5] transition flex items-center gap-2">
                            <span>Khóa học đã đăng ký</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/courses') }}" class="text-[#1D1D1F] dark:text-[#e6edf3] hover:text-[#019D91] dark:hover:text-[#02b3a5] transition flex items-center gap-2">
                            <span>Lịch sử kiểm tra & đánh giá</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ auth()->check() ? route('my-courses') : route('login') }}" class="text-[#1D1D1F] dark:text-[#e6edf3] hover:text-[#019D91] dark:hover:text-[#02b3a5] transition flex items-center gap-2">
                            <span>Chứng chỉ đã hoàn thành</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Mega Menu Footer Link -->
        <div class="mt-8 pt-6 border-t border-[#E5E5EA] dark:border-[#30363d] flex flex-wrap items-center justify-between text-xs sm:text-sm">
            <span class="text-[#86868B] dark:text-[#7d8590]">Hệ thống tiện ích học tập &amp; công cụ hỗ trợ EduLearn</span>
            <a href="{{ url('/courses') }}" class="font-semibold text-[#019D91] dark:text-[#02b3a5] hover:underline flex items-center gap-1 group">
                <span>Khám phá toàn bộ khóa học</span>
                <span class="group-hover:translate-x-1 transition-transform">→</span>
            </a>
        </div>
    </div>
</div>
