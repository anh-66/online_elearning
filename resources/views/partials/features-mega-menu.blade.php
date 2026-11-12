<!-- NavigationMenuContent: Tiện ích (@animate-ui NavigationMenu Style) -->
<div x-show="activeMenu === 'features'"
     x-cloak
     x-transition:enter="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-300"
     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
     @mouseenter="openMenu('features')"
     @mouseleave="closeMenu()"
     class="absolute left-0 sm:-left-20 top-full pt-2 z-50">
    
    <!-- NavigationMenuIndicator Caret Arrow -->
    <div class="absolute top-[3px] left-6 sm:left-[96px] size-2.5 rotate-45 bg-white dark:bg-[#161b22] border-t border-l border-gray-200/80 dark:border-[#30363d] shadow-xs z-10"></div>

    <div class="relative w-[340px] sm:w-[380px] rounded-2xl border border-gray-200/80 dark:border-[#30363d] bg-white/95 dark:bg-[#161b22]/95 backdrop-blur-2xl backdrop-saturate-150 shadow-2xl p-2.5 ring-1 ring-black/5 dark:ring-white/5 space-y-1">
        <a href="{{ auth()->check() ? route('my-courses') : route('login') }}"
           class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-black/[0.05] dark:hover:bg-white/10 transition-colors group">
            <div class="size-8 rounded-lg bg-[#019D91]/10 text-[#019D91] dark:text-[#00ddb3] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-[#019D91] dark:group-hover:text-[#00ddb3] transition-colors">Khóa học của tôi</div>
                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">Xem tiến độ và tiếp tục các khóa học</p>
            </div>
        </a>

        <a href="{{ route('courses.index') }}"
           class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-black/[0.05] dark:hover:bg-white/10 transition-colors group">
            <div class="size-8 rounded-lg bg-[#019D91]/10 text-[#019D91] dark:text-[#00ddb3] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-[#019D91] dark:group-hover:text-[#00ddb3] transition-colors">Khám phá khóa học</div>
                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">Tìm kiếm và chọn lọc khóa học yêu thích</p>
            </div>
        </a>

        @if(auth()->check() && Auth::user()->isInstructor())
            <a href="{{ route('instructor.dashboard') }}"
               class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-black/[0.05] dark:hover:bg-white/10 transition-colors group">
                <div class="size-8 rounded-lg bg-[#019D91]/10 text-[#019D91] dark:text-[#00ddb3] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-[#019D91] dark:group-hover:text-[#00ddb3] transition-colors">Studio Giảng viên</div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">Quản lý bài giảng và khóa học</p>
                </div>
            </a>
        @elseif(auth()->check() && Auth::user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-black/[0.05] dark:hover:bg-white/10 transition-colors group">
                <div class="size-8 rounded-lg bg-[#019D91]/10 text-[#019D91] dark:text-[#00ddb3] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-[#019D91] dark:group-hover:text-[#00ddb3] transition-colors">Trang Quản trị Admin</div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">Quản lý toàn bộ hệ thống EduLearn</p>
                </div>
            </a>
        @else
            <a href="{{ auth()->check() ? route('profile.edit') : route('login') }}"
               class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-black/[0.05] dark:hover:bg-white/10 transition-colors group">
                <div class="size-8 rounded-lg bg-[#019D91]/10 text-[#019D91] dark:text-[#00ddb3] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-[#019D91] dark:group-hover:text-[#00ddb3] transition-colors">Tài khoản & Hồ sơ</div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">Tùy chỉnh thông tin và mật khẩu</p>
                </div>
            </a>
        @endif
    </div>
</div>
