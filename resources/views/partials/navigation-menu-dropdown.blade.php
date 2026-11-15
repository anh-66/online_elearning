<!-- Shared Viewport Floating Card Container (@animate-ui / LottieFiles Morphing NavigationMenu Style) -->
<div x-show="activeMenu !== null"
     x-cloak
     x-transition:enter="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-250"
     x-transition:enter-start="opacity-0 translate-y-2 scale-[0.98]"
     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
     x-transition:leave-end="opacity-0 translate-y-2 scale-[0.98]"
     @mouseenter="clearTimeout(menuTimeout)"
     @mouseleave="closeMenu()"
     class="absolute left-0 top-full pt-3 z-50 pointer-events-auto">

    <!-- Gliding Caret Indicator Arrow (Trượt theo vị trí menu đang chọn) -->
    <div class="absolute top-[5px] size-4 rotate-45 bg-white dark:bg-[#161b22] border-t border-l border-gray-200 dark:border-[#30363d] shadow-xs z-10 transition-all duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
         :class="activeMenu === 'support' ? 'left-[240px]' : 'left-[350px]'">
    </div>

    <!-- Khung Viewport dàn ngang 3 cột rộng rãi, chữ to rõ nét ban đầu, không bị che khuất bo góc đáy -->
    <div class="relative rounded-2xl border border-gray-200 dark:border-[#30363d] bg-white dark:bg-[#161b22] shadow-2xl p-7 lg:p-8 ring-1 ring-black/5 dark:ring-white/5 overflow-hidden transition-all duration-300 ease-[cubic-bezier(0.22,1,0.36,1)] flex flex-col justify-between w-[960px]">
        
        <!-- ==================== TAB 1: HỖ TRỢ (3 Cột rộng rãi, chữ to rõ) ==================== -->
        <div x-show="activeMenu === 'support'"
             x-transition:enter="transition-opacity duration-300 ease-out delay-75"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-150 ease-in absolute inset-7 lg:inset-8 pb-12"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="w-full grid grid-cols-3 gap-8">
            
            <!-- Cột 1: Tài khoản & Học phí -->
            <div class="space-y-2">
                <span class="text-[13px] font-extrabold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-3.5 px-3">
                    Tài khoản & Học phí
                </span>
                
                <a href="{{ route('support.show', 'doi-mat-khau') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                    <div class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                        Tài Khoản & Mật Khẩu
                    </div>
                    <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Đổi mật khẩu, bảo mật 2 lớp và cập nhật hồ sơ cá nhân.
                    </p>
                </a>

                <a href="{{ route('support.show', 'goi-dang-ky') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                    <div class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                        Gói Học & Học Phí
                    </div>
                    <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Chính sách học phí, thanh toán an toàn và mã ưu đãi.
                    </p>
                </a>

                <a href="{{ route('support.show', 'kich-hoat-khoa-hoc') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                            Kích Hoạt Khóa Học
                        </span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-[4px] text-[10px] font-bold bg-black text-white dark:bg-white dark:text-black leading-none tracking-wide">New</span>
                    </div>
                    <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Mở khóa bài giảng và tài liệu học ngay sau khi mua.
                    </p>
                </a>
            </div>

            <!-- Cột 2: Trải nghiệm học tập -->
            <div class="space-y-2">
                <span class="text-[13px] font-extrabold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-3.5 px-3">
                    Trải nghiệm học tập
                </span>

                <a href="{{ route('support.show', 'kich-hoat-khoa-hoc') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                    <div class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                        Phòng Học & Video
                    </div>
                    <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Chỉnh tốc độ phát, ghi chú thông minh và tài liệu học.
                    </p>
                </a>

                <a href="{{ route('support.show', 'chung-chi-hoan-thanh') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                    <div class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                        Chứng Chỉ Tốt Nghiệp
                    </div>
                    <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Điều kiện nhận và cách xuất chứng chỉ khóa học.
                    </p>
                </a>

                <a href="{{ route('support.show', 'huong-dan-giang-vien') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                    <div class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                        Studio Giảng Viên
                    </div>
                    <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Hướng dẫn tạo bài giảng và quản lý học viên.
                    </p>
                </a>
            </div>

            <!-- Cột 3: Trợ giúp & Phản hồi -->
            <div class="space-y-2">
                <span class="text-[13px] font-extrabold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-3.5 px-3">
                    Trợ giúp & Phản hồi
                </span>

                <a href="{{ route('support.index') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                    <div class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                        Trung Tâm Trợ Giúp (FAQ)
                    </div>
                    <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Tổng hợp lời giải đáp cho các câu hỏi phổ biến nhất.
                    </p>
                </a>

                <a href="{{ route('support.feedback') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                            Báo Lỗi & Phản Hồi
                        </span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-[4px] text-[10px] font-bold bg-black text-white dark:bg-white dark:text-black leading-none tracking-wide">New</span>
                    </div>
                    <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Báo cáo sự cố hoặc gửi ý kiến góp ý cho EduLearn.
                    </p>
                </a>

                <a href="{{ route('support.show', 'goi-dang-ky') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                    <div class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                        Cam Kết Chất Lượng
                    </div>
                    <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Hoàn tiền minh bạch trong 7 ngày nếu không hài lòng.
                    </p>
                </a>
            </div>
        </div>

        <!-- ==================== TAB 2: TIỆN ÍCH (Dàn ngang 3 Cột, giữ nguyên chữ to rõ nét) ==================== -->
        <div x-show="activeMenu === 'features'"
             x-transition:enter="transition-opacity duration-300 ease-out delay-75"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity duration-150 ease-in absolute inset-7 lg:inset-8 pb-12"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="w-full grid grid-cols-3 gap-8">
            
            <!-- Cột 1: Không gian học tập -->
            <div class="space-y-2">
                <span class="text-[13px] font-extrabold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-3.5 px-3">
                    Không gian học tập
                </span>

                <a href="{{ auth()->check() ? route('my-courses') : route('login') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                    <div class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                        Khóa Học Của Tôi
                    </div>
                    <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Theo dõi tiến độ và tiếp tục bài giảng đang học dở.
                    </p>
                </a>

                <a href="{{ route('courses.index') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                    <div class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                        Khám Phá Khóa Học
                    </div>
                    <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Hàng trăm khóa học lập trình, AI và kỹ năng số.
                    </p>
                </a>

                <a href="{{ auth()->check() ? route('certificates.index') : route('login') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                            Chứng Chỉ Của Tôi
                        </span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-[4px] text-[10px] font-bold bg-black text-white dark:bg-white dark:text-black leading-none tracking-wide">New</span>
                    </div>
                    <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Xem và xuất chứng chỉ tốt nghiệp kèm mã QR.
                    </p>
                </a>
            </div>

            <!-- Cột 2: Thi đua & Hoạt động -->
            <div class="space-y-2">
                <span class="text-[13px] font-extrabold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-3.5 px-3">
                    Thi đua & Hoạt động
                </span>

                <a href="{{ route('leaderboard.index') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                            Bảng Xếp Hạng
                        </span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-[4px] text-[10px] font-bold bg-amber-500 text-white leading-none tracking-wide">Top 10</span>
                    </div>
                    <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Vinh danh học viên có điểm EXP thành tựu cao nhất.
                    </p>
                </a>

                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                    <div class="flex items-center gap-2">
                        <span class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                            Điểm Danh Hằng Ngày
                        </span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-[4px] text-[10px] font-bold bg-black text-white dark:bg-white dark:text-black leading-none tracking-wide">New</span>
                    </div>
                    <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Duy trì chuỗi học tập và nhận phần thưởng điểm.
                    </p>
                </a>
            </div>

            <!-- Cột 3: Tiện ích & Quản lý -->
            <div class="space-y-2">
                <span class="text-[13px] font-extrabold text-gray-400 dark:text-gray-500 uppercase tracking-wider block mb-3.5 px-3">
                    Tiện ích & Quản lý
                </span>

                @if(auth()->check() && Auth::user()->isInstructor())
                    <a href="{{ route('instructor.dashboard') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                        <div class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                            Studio Giảng Viên
                        </div>
                        <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                            Quản lý bài học, học viên và theo dõi thu nhập.
                        </p>
                    </a>
                @elseif(auth()->check() && Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                        <div class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                            Trang Quản Trị Admin
                        </div>
                        <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                            Quản trị toàn diện hệ thống EduLearn và người dùng.
                        </p>
                    </a>
                @else
                    <a href="{{ route('courses.index') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                        <div class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                            Ưu Đãi Học Viên
                        </div>
                        <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                            Chương trình học bổng và mã giảm giá hôm nay.
                        </p>
                    </a>
                @endif

                <a href="{{ auth()->check() ? route('profile.edit') : route('login') }}" class="group block p-3.5 rounded-2xl hover:bg-black/[0.04] dark:hover:bg-white/[0.06] transition-colors">
                    <div class="text-[16px] font-bold text-gray-900 dark:text-white transition-colors leading-snug">
                        Hồ Sơ & Giao Diện
                    </div>
                    <p class="text-[13.5px] text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                        Cập nhật thông tin cá nhân và bật/tắt Dark Mode.
                    </p>
                </a>
            </div>
        </div>

        <!-- ==================== FOOTER DƯỚI CÙNG (Chữ to rõ nét ban đầu) ==================== -->
        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-[#30363d] px-3 flex items-center justify-end text-[13.5px] sm:text-[14px]">
            <template x-if="activeMenu === 'support'">
                <div class="flex items-center justify-end w-full">
                    <a href="{{ route('support.feedback') }}" class="font-bold text-gray-900 dark:text-white hover:underline flex items-center gap-1.5 group">
                        <span>Gửi phản hồi / Gặp tư vấn viên</span>
                        <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </template>
            <template x-if="activeMenu === 'features'">
                <div class="flex items-center justify-end w-full">
                    <a href="{{ route('courses.index') }}" class="font-bold text-gray-900 dark:text-white hover:underline flex items-center gap-1.5 group">
                        <span>Khám phá toàn bộ khóa học</span>
                        <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </template>
        </div>

    </div>
</div>