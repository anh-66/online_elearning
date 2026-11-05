<!-- Apple Style Support Mega Menu Flyout -->
<div x-show="supportFlyoutOpen"
     x-cloak
     x-transition:enter="transition ease-out duration-250"
     x-transition:enter-start="opacity-0 -translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2"
     @mouseenter="openSupportFlyout()"
     @mouseleave="scheduleSupportClose()"
     class="absolute left-0 right-0 top-full w-full bg-white dark:bg-[#0d1117] border-b border-[#E5E5EA] dark:border-[#30363d] shadow-2xl z-40 transition-colors">

    <div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-8 lg:px-12 py-8 sm:py-10">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12">
            <!-- Column 1: Khám Phá Hỗ Trợ (Big Bold Typography like Apple Support) -->
            <div class="md:col-span-5 space-y-4">
                <span class="text-xs font-semibold text-[#86868B] dark:text-[#7d8590] tracking-wider uppercase block">
                    Khám Phá Hỗ Trợ
                </span>
                <nav class="flex flex-col space-y-2 sm:space-y-2.5">
                    <a href="{{ route('support.show', 'doi-mat-khau') }}"
                       class="text-xl sm:text-2xl font-bold text-[#1D1D1F] dark:text-white hover:text-[#019D91] dark:hover:text-[#02b3a5] transition duration-150 tracking-tight flex items-center justify-between group">
                        <span>Tài Khoản & Mật Khẩu</span>
                        <svg class="w-5 h-5 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition text-[#019D91]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('support.show', 'goi-dang-ky') }}"
                       class="text-xl sm:text-2xl font-bold text-[#1D1D1F] dark:text-white hover:text-[#019D91] dark:hover:text-[#02b3a5] transition duration-150 tracking-tight flex items-center justify-between group">
                        <span>Gói Đăng Ký & Học Phí</span>
                        <svg class="w-5 h-5 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition text-[#019D91]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('support.show', 'kich-hoat-khoa-hoc') }}"
                       class="text-xl sm:text-2xl font-bold text-[#1D1D1F] dark:text-white hover:text-[#019D91] dark:hover:text-[#02b3a5] transition duration-150 tracking-tight flex items-center justify-between group">
                        <span>Phòng Học & Video Player</span>
                        <svg class="w-5 h-5 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition text-[#019D91]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('support.show', 'chung-chi-hoan-thanh') }}"
                       class="text-xl sm:text-2xl font-bold text-[#1D1D1F] dark:text-white hover:text-[#019D91] dark:hover:text-[#02b3a5] transition duration-150 tracking-tight flex items-center justify-between group">
                        <span>Chứng Chỉ Tốt Nghiệp</span>
                        <svg class="w-5 h-5 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition text-[#019D91]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('support.show', 'huong-dan-giang-vien') }}"
                       class="text-xl sm:text-2xl font-bold text-[#1D1D1F] dark:text-white hover:text-[#019D91] dark:hover:text-[#02b3a5] transition duration-150 tracking-tight flex items-center justify-between group">
                        <span>Studio Giảng Viên</span>
                        <svg class="w-5 h-5 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition text-[#019D91]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </nav>
            </div>

            <!-- Column 2: Trợ Giúp (Medium Regular Links) -->
            <div class="md:col-span-3 space-y-4">
                <span class="text-xs font-semibold text-[#86868B] dark:text-[#7d8590] tracking-wider uppercase block">
                    Trợ Giúp
                </span>
                <ul class="space-y-3 text-sm font-medium">
                    <li>
                        <a href="{{ route('support.index') }}" class="text-[#1D1D1F] dark:text-[#e6edf3] hover:text-[#019D91] dark:hover:text-[#02b3a5] transition flex items-center gap-2">
                            <span>Trung Tâm Trợ Giúp</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('support.index') }}#faq" class="text-[#1D1D1F] dark:text-[#e6edf3] hover:text-[#019D91] dark:hover:text-[#02b3a5] transition flex items-center gap-2">
                            <span>Câu Hỏi Thường Gặp (FAQ)</span>
                        </a>
                    </li>
                    @auth
                        @if(Route::has('my-courses'))
                            <li>
                                <a href="{{ route('my-courses') }}" class="text-[#1D1D1F] dark:text-[#e6edf3] hover:text-[#019D91] dark:hover:text-[#02b3a5] transition flex items-center gap-2">
                                    <span>Khóa Học Của Tôi</span>
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('profile.edit') }}" class="text-[#1D1D1F] dark:text-[#e6edf3] hover:text-[#019D91] dark:hover:text-[#02b3a5] transition flex items-center gap-2">
                                <span>Cài Đặt Hồ Sơ Cá Nhân</span>
                            </a>
                        </li>
                    @endauth
                    <li>
                        <a href="{{ route('support.feedback') }}" class="text-[#1D1D1F] dark:text-[#e6edf3] hover:text-[#019D91] dark:hover:text-[#02b3a5] transition flex items-center gap-2">
                            <span>Báo Cáo Sự Cố & Góp Ý</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Column 3: Chủ Đề Hữu Ích (Quick Action Links) -->
            <div class="md:col-span-4 space-y-4">
                <span class="text-xs font-semibold text-[#86868B] dark:text-[#7d8590] tracking-wider uppercase block">
                    Chủ Đề Hữu Ích
                </span>
                <ul class="space-y-3 text-sm font-medium">
                    <li>
                        <a href="{{ route('support.show', 'doi-mat-khau') }}" class="text-[#1D1D1F] dark:text-[#e6edf3] hover:text-[#019D91] dark:hover:text-[#02b3a5] transition">
                            Đổi Mật Khẩu Tài Khoản
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('support.show', 'dat-lai-mat-khau') }}" class="text-[#1D1D1F] dark:text-[#e6edf3] hover:text-[#019D91] dark:hover:text-[#02b3a5] transition">
                            Đặt Lại Mật Khẩu Khi Quên
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('support.show', 'goi-dang-ky') }}" class="text-[#1D1D1F] dark:text-[#e6edf3] hover:text-[#019D91] dark:hover:text-[#02b3a5] transition">
                            Thanh Toán & Gói Đăng Ký Khóa Học
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('support.show', 'kich-hoat-khoa-hoc') }}" class="text-[#1D1D1F] dark:text-[#e6edf3] hover:text-[#019D91] dark:hover:text-[#02b3a5] transition">
                            Hướng Dẫn Sử Dụng Trình Phát Video
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('support.show', 'chung-chi-hoan-thanh') }}" class="text-[#1D1D1F] dark:text-[#e6edf3] hover:text-[#019D91] dark:hover:text-[#02b3a5] transition">
                            Nhận Chứng Chỉ Tốt Nghiệp Hợp Lệ
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Mega Menu Footer Link -->
        <div class="mt-8 pt-6 border-t border-[#E5E5EA] dark:border-[#30363d] flex flex-wrap items-center justify-between text-xs sm:text-sm">
            <span class="text-[#86868B] dark:text-[#7d8590]">Trung tâm Hỗ trợ kỹ thuật & dịch vụ học tập EduLearn</span>
            <a href="{{ route('support.index') }}" class="font-semibold text-[#019D91] dark:text-[#02b3a5] hover:underline flex items-center gap-1 group">
                <span>Khám phá toàn bộ trang hỗ trợ</span>
                <span class="group-hover:translate-x-1 transition-transform">→</span>
            </a>
        </div>
    </div>
</div>
