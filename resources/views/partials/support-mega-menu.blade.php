<!-- NavigationMenuContent: Hỗ Trợ (@animate-ui NavigationMenu Style) -->
<div x-show="activeMenu === 'support'"
     x-cloak
     x-transition:enter="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-300"
     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
     @mouseenter="openMenu('support')"
     @mouseleave="closeMenu()"
     class="absolute left-0 top-full pt-2 z-50">
    
    <!-- NavigationMenuIndicator Caret Arrow -->
    <div class="absolute top-[3px] left-6 size-2.5 rotate-45 bg-white dark:bg-[#161b22] border-t border-l border-gray-200/80 dark:border-[#30363d] shadow-xs z-10"></div>

    <div class="relative w-[480px] sm:w-[540px] rounded-2xl border border-gray-200/80 dark:border-[#30363d] bg-white/95 dark:bg-[#161b22]/95 backdrop-blur-2xl backdrop-saturate-150 shadow-2xl p-3 ring-1 ring-black/5 dark:ring-white/5">
        <ul class="grid grid-cols-2 gap-1.5">
            <li>
                <a href="{{ route('support.show', 'doi-mat-khau') }}"
                   class="flex flex-col gap-1 p-2.5 rounded-xl hover:bg-black/[0.05] dark:hover:bg-white/10 transition-colors group">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-[#019D91] dark:group-hover:text-[#00ddb3] transition-colors flex items-center justify-between">
                        <span>Tài Khoản & Mật Khẩu</span>
                        <svg class="size-3.5 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition text-[#019D91]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">Bảo mật tài khoản, đổi mật khẩu và cập nhật thông tin cá nhân.</p>
                </a>
            </li>
            <li>
                <a href="{{ route('support.show', 'goi-dang-ky') }}"
                   class="flex flex-col gap-1 p-2.5 rounded-xl hover:bg-black/[0.05] dark:hover:bg-white/10 transition-colors group">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-[#019D91] dark:group-hover:text-[#00ddb3] transition-colors flex items-center justify-between">
                        <span>Gói Học & Học Phí</span>
                        <svg class="size-3.5 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition text-[#019D91]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">Chính sách học phí, thanh toán và kích hoạt khóa học.</p>
                </a>
            </li>
            <li>
                <a href="{{ route('support.show', 'kich-hoat-khoa-hoc') }}"
                   class="flex flex-col gap-1 p-2.5 rounded-xl hover:bg-black/[0.05] dark:hover:bg-white/10 transition-colors group">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-[#019D91] dark:group-hover:text-[#00ddb3] transition-colors flex items-center justify-between">
                        <span>Phòng Học & Video</span>
                        <svg class="size-3.5 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition text-[#019D91]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">Cách học hiệu quả, tài liệu bài học và xử lý lỗi phát video.</p>
                </a>
            </li>
            <li>
                <a href="{{ route('support.show', 'chung-chi-hoan-thanh') }}"
                   class="flex flex-col gap-1 p-2.5 rounded-xl hover:bg-black/[0.05] dark:hover:bg-white/10 transition-colors group">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-[#019D91] dark:group-hover:text-[#00ddb3] transition-colors flex items-center justify-between">
                        <span>Chứng Chỉ Tốt Nghiệp</span>
                        <svg class="size-3.5 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition text-[#019D91]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">Điều kiện và cách tải chứng chỉ sau khi hoàn thành khóa.</p>
                </a>
            </li>
            <li>
                <a href="{{ route('support.index') }}"
                   class="flex flex-col gap-1 p-2.5 rounded-xl hover:bg-black/[0.05] dark:hover:bg-white/10 transition-colors group">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-[#019D91] dark:group-hover:text-[#00ddb3] transition-colors flex items-center justify-between">
                        <span>Trung Tâm Trợ Giúp (FAQ)</span>
                        <svg class="size-3.5 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition text-[#019D91]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">Tổng hợp câu hỏi và giải đáp thắc mắc thường gặp.</p>
                </a>
            </li>
            <li>
                <a href="{{ route('support.feedback') }}"
                   class="flex flex-col gap-1 p-2.5 rounded-xl hover:bg-black/[0.05] dark:hover:bg-white/10 transition-colors group">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-[#019D91] dark:group-hover:text-[#00ddb3] transition-colors flex items-center justify-between">
                        <span>Gửi Phản Hồi & Yêu Cầu</span>
                        <svg class="size-3.5 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition text-[#019D91]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">Báo lỗi hệ thống hoặc gửi ý kiến đóng góp cho EduLearn.</p>
                </a>
            </li>
        </ul>
        <div class="mt-2 pt-2 border-t border-gray-100 dark:border-[#30363d]/80 px-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
            <span>Cần trợ giúp thêm?</span>
            <a href="{{ route('support.feedback') }}" class="font-medium text-[#019D91] dark:text-[#00ddb3] hover:underline flex items-center gap-1">
                Gửi phản hồi
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</div>
