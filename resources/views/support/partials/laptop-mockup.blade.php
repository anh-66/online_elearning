@props(['type' => 'password_change', 'title' => 'EduLearn - Hướng dẫn'])

<div class="relative w-full max-w-2xl mx-auto my-6 select-none group">
    <!-- Instruction Screen -->
    <div class="relative mx-auto overflow-hidden rounded-2xl sm:rounded-3xl border border-[#D2D2D7] bg-white shadow-lg shadow-black/[0.08] dark:border-[#30363d] dark:bg-[#0d1117]">
        <!-- Browser Window -->
        <div class="relative bg-white dark:bg-[#0d1117]">
            <!-- Window Browser Bar -->
            <div class="flex items-center justify-between px-3 py-2 bg-[#F5F5F7] dark:bg-[#161b22] border-b border-[#E5E5EA] dark:border-[#30363d] text-xs">
                <!-- Window Controls -->
                <div class="flex items-center space-x-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#FF5F56] inline-block shadow-sm"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-[#FFBD2E] inline-block shadow-sm"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-[#27C93F] inline-block shadow-sm"></span>
                </div>

                <!-- URL Bar -->
                <div class="flex items-center justify-center flex-1 max-w-xs mx-auto px-2.5 py-1 rounded-md bg-white dark:bg-[#0d1117] border border-[#E5E5EA] dark:border-[#30363d] text-[11px] text-[#86868B] dark:text-[#7d8590]">
                    <svg class="w-3 h-3 mr-1.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="truncate font-mono">
                        @switch($type)
                            @case('password_change')
                                edulearn.vn/profile#password
                                @break
                            @case('password_reset')
                                edulearn.vn/forgot-password
                                @break
                            @case('subscription')
                                edulearn.vn/courses?plan=lifetime
                                @break
                            @case('player')
                                edulearn.vn/learning/player
                                @break
                            @case('certificate')
                                edulearn.vn/certificate/verify/EDU-88921
                                @break
                            @case('instructor')
                                edulearn.vn/instructor/courses/create
                                @break
                            @default
                                edulearn.vn/support
                        @endswitch
                    </span>
                </div>

                <div class="text-[11px] text-[#86868B] dark:text-[#7d8590] hidden sm:block">EduLearn</div>
            </div>

            <!-- Screen View Content Based on Type -->
            <div class="p-4 sm:p-6 min-h-[220px] sm:min-h-[260px] flex flex-col justify-center bg-[#FAFAFC] dark:bg-[#0d1117]">
                @if($type === 'password_change')
                    <!-- Password Change Mockup Screen -->
                    <div class="max-w-md mx-auto w-full bg-white dark:bg-[#161b22] p-4 sm:p-5 rounded-2xl border border-[#E5E5EA] dark:border-[#30363d] shadow-sm">
                        <div class="flex items-center space-x-3 pb-3 border-b border-[#E5E5EA] dark:border-[#30363d] mb-3">
                            <div class="w-9 h-9 rounded-full bg-[#019D91]/10 text-[#019D91] flex items-center justify-center font-bold text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-semibold text-[#1D1D1F] dark:text-white">Cập nhật mật khẩu tài khoản</h4>
                                <p class="text-[11px] text-[#86868B] dark:text-[#7d8590]">Đảm bảo an toàn cho tài khoản cá nhân</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div>
                                <label class="text-[10px] font-medium text-[#86868B] dark:text-[#7d8590] uppercase">Mật khẩu hiện tại</label>
                                <div class="h-7 px-2.5 rounded-lg bg-[#F5F5F7] dark:bg-[#21262d] border border-[#E5E5EA] dark:border-[#30363d] flex items-center text-xs tracking-widest text-[#86868B]">••••••••••</div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-[10px] font-medium text-[#86868B] dark:text-[#7d8590] uppercase">Mật khẩu mới</label>
                                    <div class="h-7 px-2.5 rounded-lg bg-white dark:bg-[#0d1117] border border-[#019D91] flex items-center text-xs tracking-widest text-[#019D91]">••••••••••••</div>
                                </div>
                                <div>
                                    <label class="text-[10px] font-medium text-[#86868B] dark:text-[#7d8590] uppercase">Xác nhận lại</label>
                                    <div class="h-7 px-2.5 rounded-lg bg-white dark:bg-[#0d1117] border border-[#019D91] flex items-center text-xs tracking-widest text-[#019D91]">••••••••••••</div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-2">
                                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Độ mạnh: Rất an toàn
                                </span>
                                <div class="px-3 py-1 bg-[#019D91] text-white text-[11px] font-semibold rounded-lg shadow-sm">
                                    Lưu thay đổi
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($type === 'password_reset')
                    <!-- Forgot Password Mockup Screen -->
                    <div class="max-w-md mx-auto w-full bg-white dark:bg-[#161b22] p-4 sm:p-5 rounded-2xl border border-[#E5E5EA] dark:border-[#30363d] shadow-sm text-center">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="text-xs sm:text-sm font-semibold text-[#1D1D1F] dark:text-white mb-1">Khôi phục mật khẩu qua Email</h4>
                        <p class="text-[11px] text-[#86868B] dark:text-[#7d8590] mb-3">Điền email của bạn để nhận đường dẫn đặt lại an toàn</p>
                        <div class="h-8 px-3 rounded-lg bg-[#F5F5F7] dark:bg-[#21262d] border border-[#E5E5EA] dark:border-[#30363d] flex items-center text-xs text-[#1D1D1F] dark:text-white mb-2.5">
                            hocvien@edulearn.vn
                        </div>
                        <div class="w-full py-1.5 bg-[#019D91] text-white text-xs font-semibold rounded-lg shadow-sm">
                            Gửi liên kết đặt lại mật khẩu
                        </div>
                    </div>

                @elseif($type === 'subscription')
                    <!-- Subscription & Courses Catalog Screen -->
                    <div class="grid grid-cols-2 gap-3 max-w-lg mx-auto w-full">
                        <div class="bg-white dark:bg-[#161b22] p-3.5 rounded-xl border border-[#E5E5EA] dark:border-[#30363d] shadow-sm">
                            <div class="text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">Khóa Miễn Phí</div>
                            <div class="text-sm font-bold text-[#1D1D1F] dark:text-white mt-1">Cộng Đồng Học Tập</div>
                            <div class="text-xs text-[#86868B] dark:text-[#7d8590] mt-1">Học không giới hạn thời gian</div>
                            <div class="mt-3 py-1 text-center bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[11px] font-medium rounded-lg">
                                Ghi danh ngay (0đ)
                            </div>
                        </div>
                        <div class="bg-[#E8F7F5] dark:bg-[#123331] p-3.5 rounded-xl border border-[#019D91]/40 shadow-sm relative overflow-hidden">
                            <div class="text-[10px] font-semibold text-[#018076] dark:text-[#02b3a5] uppercase tracking-wide">Khóa Chuyên Sâu VIP</div>
                            <div class="text-sm font-bold text-[#1D1D1F] dark:text-white mt-1">Sở Hữu Trọn Đời</div>
                            <div class="text-xs text-[#86868B] dark:text-[#7d8590] mt-1">Kèm chứng chỉ & dự án mẫu</div>
                            <div class="mt-3 py-1 text-center bg-[#019D91] text-white text-[11px] font-semibold rounded-lg shadow-sm">
                                Đăng ký khóa học
                            </div>
                        </div>
                    </div>

                @elseif($type === 'player')
                    <!-- Video Player Screen -->
                    <div class="bg-[#161b22] text-white rounded-xl overflow-hidden border border-[#30363d] shadow-sm max-w-lg mx-auto w-full">
                        <div class="aspect-video bg-black/60 relative flex items-center justify-center">
                            <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur text-white flex items-center justify-center ring-2 ring-white/30">
                                <svg class="w-6 h-6 ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M4.5 3.5l11 6.5-11 6.5v-13z"/></svg>
                            </div>
                            <div class="absolute bottom-2 left-3 right-3 flex items-center space-x-2 text-[10px] text-white/80">
                                <span>12:45</span>
                                <div class="flex-1 h-1 bg-white/30 rounded-full overflow-hidden">
                                    <div class="w-2/3 h-full bg-[#019D91]"></div>
                                </div>
                                <span>18:30</span>
                            </div>
                        </div>
                        <div class="p-2.5 flex items-center justify-between text-xs bg-[#0d1117]">
                            <span class="font-medium truncate">Bài 4: Hướng dẫn cấu trúc & Cài đặt</span>
                            <span class="text-emerald-400 font-semibold flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                65% Hoàn thành
                            </span>
                        </div>
                    </div>

                @elseif($type === 'certificate')
                    <!-- Certificate Mockup Screen -->
                    <div class="bg-white dark:bg-[#161b22] border-2 border-amber-400/50 p-4 sm:p-5 rounded-xl shadow-md max-w-md mx-auto w-full text-center relative overflow-hidden">
                        <div class="text-[9px] uppercase tracking-widest text-amber-600 dark:text-amber-400 font-semibold">Certificate of Completion</div>
                        <h4 class="text-sm font-bold text-[#1D1D1F] dark:text-white mt-1">Chứng Chỉ Tốt Nghiệp Khóa Học</h4>
                        <div class="w-16 h-0.5 bg-amber-400 mx-auto my-2"></div>
                        <p class="text-xs text-[#86868B] dark:text-[#7d8590]">Trao tặng cho Học viên xuất sắc</p>
                        <div class="text-sm font-semibold text-[#019D91] mt-1">Nguyễn Văn A</div>
                        <div class="mt-3 pt-2 border-t border-[#E5E5EA] dark:border-[#30363d] flex items-center justify-between text-[10px] text-[#86868B]">
                            <span>ID: EDU-2026-X892</span>
                            <span class="text-emerald-600 font-medium">✓ Đã xác thực QR</span>
                        </div>
                    </div>

                @else
                    <!-- Instructor Studio Screen -->
                    <div class="bg-white dark:bg-[#161b22] p-4 rounded-xl border border-[#E5E5EA] dark:border-[#30363d] shadow-sm max-w-md mx-auto w-full">
                        <div class="flex items-center justify-between pb-2 border-b border-[#E5E5EA] dark:border-[#30363d]">
                            <span class="text-xs font-semibold text-[#1D1D1F] dark:text-white">Studio Quản Lý Bài Giảng</span>
                            <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-600 text-[10px] font-semibold rounded-full">Đang xuất bản</span>
                        </div>
                        <div class="mt-2.5 space-y-1.5 text-xs">
                            <div class="p-2 rounded bg-[#F5F5F7] dark:bg-[#21262d] flex justify-between items-center">
                                <span>Chương 1: Khởi động dự án</span>
                                <span class="text-[#86868B] text-[10px]">5 bài học</span>
                            </div>
                            <div class="p-2 rounded bg-[#F5F5F7] dark:bg-[#21262d] flex justify-between items-center">
                                <span>Chương 2: Xây dựng cơ sở dữ liệu</span>
                                <span class="text-[#86868B] text-[10px]">8 bài học</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
