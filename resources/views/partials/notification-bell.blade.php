@php
    $bellNotifications = \App\Models\Notification::latest()->limit(15)->get()->map(function ($item) {
        return [
            'id' => $item->id,
            'title' => $item->title,
            'content' => $item->content,
            'type' => $item->type,
            'time' => $item->created_at?->diffForHumans() ?? '',
        ];
    });
@endphp

<div class="relative"
     x-data="{
        open: false,
        notifications: {{ Js::from($bellNotifications) }},
        readIds: JSON.parse(localStorage.getItem('read_notifications') || '[]'),
        get unreadCount() {
            return this.notifications.filter(n => !this.readIds.includes(n.id)).length;
        },
        isRead(id) {
            return this.readIds.includes(id);
        },
        markAllAsRead() {
            this.readIds = this.notifications.map(n => n.id);
            try {
                localStorage.setItem('read_notifications', JSON.stringify(this.readIds));
            } catch (e) {}
        },
        markAsRead(id) {
            if (!this.readIds.includes(id)) {
                this.readIds.push(id);
                try {
                    localStorage.setItem('read_notifications', JSON.stringify(this.readIds));
                } catch (e) {}
            }
        }
     }"
     @click.outside="open = false"
     @keydown.escape.window="open = false">

    <!-- Quả chuông Thông Báo (SVG chuẩn vector, đổi màu linh hoạt theo sáng/tối) -->
    <button type="button"
            @click="open = !open"
            aria-label="Thông báo"
            :title="unreadCount > 0 ? (unreadCount + ' thông báo chưa đọc') : 'Thông báo'"
            class="relative inline-flex h-9 w-9 items-center justify-center rounded-full text-[#1D1D1F] hover:bg-black/5 dark:text-[#e6edf3] dark:hover:bg-[#21262d] transition-colors focus:outline-none focus:ring-2 focus:ring-[#019D91]">
        
        <!-- SVG Quả chuông (Vector chuẩn, không dùng emoji) -->
        <svg class="w-5 h-5 transition-transform duration-200"
             :class="{ 'rotate-12': unreadCount > 0 && !open }"
             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        <!-- Badge Số lượng tin chưa đọc -->
        <span x-show="unreadCount > 0"
              x-cloak
              x-transition.scale.origin.top.right
              class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-rose-600 rounded-full shadow-sm ring-2 ring-white dark:ring-[#0d1117] leading-none"
              x-text="unreadCount > 9 ? '9+' : unreadCount">
        </span>
    </button>

    <!-- Dropdown Menu Danh Sách Thông Báo -->
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 sm:w-96 max-w-[calc(100vw-2rem)] bg-white dark:bg-[#161b22] text-[#1D1D1F] dark:text-[#e6edf3] rounded-2xl shadow-2xl border border-[#E5E5EA] dark:border-[#30363d] overflow-hidden z-50 text-sm">

        <!-- Header Dropdown -->
        <div class="px-4 py-3.5 border-b border-[#E5E5EA] dark:border-[#30363d] flex items-center justify-between bg-white dark:bg-[#161b22]">
            <div class="flex items-center gap-2">
                <h3 class="font-bold text-sm text-[#1D1D1F] dark:text-white">Thông báo</h3>
                <span x-show="unreadCount > 0"
                      x-cloak
                      class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-100 dark:border-rose-900"
                      x-text="unreadCount + ' chưa đọc'">
                </span>
            </div>

            <!-- Nút Đánh dấu đã đọc tất cả -->
            <button type="button"
                    @click="markAllAsRead()"
                    x-show="unreadCount > 0"
                    x-cloak
                    class="text-xs font-semibold text-[#019D91] hover:text-[#008075] dark:text-[#02b3a5] dark:hover:text-[#00d8c5] transition focus:outline-none">
                Đánh dấu đã đọc tất cả
            </button>
        </div>

        <!-- Danh sách thông báo (Cuộn tối đa 420px) -->
        <div class="max-h-[420px] overflow-y-auto divide-y divide-[#E5E5EA] dark:divide-[#30363d]">
            <template x-if="notifications.length === 0">
                <div class="py-12 px-4 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 dark:bg-[#21262d] flex items-center justify-center text-gray-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <p class="text-xs text-[#86868B] dark:text-[#8b949e]">Hiện tại chưa có thông báo mới nào.</p>
                </div>
            </template>

            <template x-for="item in notifications" :key="item.id">
                <div @click="markAsRead(item.id)"
                     class="p-4 transition cursor-pointer flex items-start gap-3 relative hover:bg-black/[0.02] dark:hover:bg-[#21262d]/60"
                     :class="{
                         'bg-sky-50/40 dark:bg-[#1a2333]/50': !isRead(item.id),
                         'opacity-75': isRead(item.id)
                     }">

                    <!-- Icon Vector theo loại thông báo (Không dùng emoji) -->
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-white shadow-sm"
                         :class="{
                             'bg-blue-600': item.type === 'system',
                             'bg-emerald-600': item.type === 'course',
                             'bg-amber-500': item.type === 'promotion',
                             'bg-slate-600': !['system', 'course', 'promotion'].includes(item.type)
                         }">
                        <!-- Hệ thống (Gear SVG) -->
                        <svg x-show="item.type === 'system'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>

                        <!-- Khóa học (Book SVG) -->
                        <svg x-show="item.type === 'course'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>

                        <!-- Khuyến mãi (Tag/Sparkle SVG) -->
                        <svg x-show="item.type === 'promotion'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>

                        <!-- Mặc định (Bell mini SVG) -->
                        <svg x-show="!['system', 'course', 'promotion'].includes(item.type)" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>

                    <!-- Nội dung thông báo -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <p class="font-semibold text-xs sm:text-sm text-[#1D1D1F] dark:text-[#f0f6fc] truncate"
                               x-text="item.title"></p>
                            <!-- Chấm xanh báo tin chưa đọc -->
                            <span x-show="!isRead(item.id)" class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                        </div>
                        <p class="text-xs text-[#86868B] dark:text-[#8b949e] line-clamp-2 leading-relaxed"
                           x-text="item.content"></p>
                        <span class="text-[10px] font-medium text-[#86868B] dark:text-[#7d8590] mt-1.5 block"
                              x-text="item.time"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
