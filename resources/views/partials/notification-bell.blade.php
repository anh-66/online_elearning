@php
    $bellNotifications = \App\Models\Notification::query()
        ->latest()
        ->get()
        ->map(fn (\App\Models\Notification $item): array => [
            'id' => $item->id,
            'title' => $item->title,
            'content' => $item->content,
            'type' => $item->type,
            'time' => $item->created_at?->diffForHumans() ?? '',
        ]);
@endphp

<div class="relative"
     data-slot="popover"
     x-data="{
         open: false,
         filter: 'all',
         showAll: false,
         notifications: {{ Js::from($bellNotifications) }},
         readIds: JSON.parse(localStorage.getItem('read_notifications') || '[]'),
         hoveredNotification: null,
         detailStyle: {},
         hideTimer: null,
         get unreadCount() {
             return this.notifications.filter(notification => !this.readIds.includes(notification.id)).length;
         },
         get visibleNotifications() {
             const filteredNotifications = this.filter === 'unread'
                 ? this.notifications.filter(notification => !this.readIds.includes(notification.id))
                 : this.notifications;

             return this.showAll ? filteredNotifications : filteredNotifications.slice(0, 2);
         },
         isRead(id) {
             return this.readIds.includes(id);
         },
         markAsRead(id) {
             if (this.isRead(id)) {
                 return;
             }

             this.readIds.push(id);
             localStorage.setItem('read_notifications', JSON.stringify(this.readIds));
         },
         markAllAsRead() {
             this.readIds = this.notifications.map(notification => notification.id);
             localStorage.setItem('read_notifications', JSON.stringify(this.readIds));
         },
         showDetails(notification, event) {
             if (window.matchMedia('(hover: none)').matches) {
                 return;
             }

             clearTimeout(this.hideTimer);
             const rect = event.currentTarget.getBoundingClientRect();
             const detailWidth = 320;
             const fitsRight = rect.right + detailWidth <= window.innerWidth - 8;
             this.hoveredNotification = notification;
             this.detailStyle = {
                 top: `${Math.max(12, rect.top)}px`,
                 ...(fitsRight
                     ? { left: `${rect.right}px` }
                     : { left: `${Math.max(8, rect.left - detailWidth)}px` })
             };
         },
         hideDetails() {
             this.hideTimer = window.setTimeout(() => {
                 this.hoveredNotification = null;
             }, 120);
         }
     }"
     @click.outside="open = false; hideDetails()"
     @keydown.escape.window="open = false; hideDetails()">
    <button type="button"
            @click="open = !open"
            data-slot="popover-trigger"
            :aria-expanded="open"
            aria-label="Thông báo"
            :title="unreadCount > 0 ? (unreadCount + ' thông báo chưa đọc') : 'Thông báo'"
            class="relative inline-flex h-9 w-9 items-center justify-center rounded-full text-[#1D1D1F] transition-colors hover:bg-black/5 focus:outline-none focus:ring-2 focus:ring-[#019D91] dark:text-[#e6edf3] dark:hover:bg-[#21262d]">
        <svg class="h-5 w-5" :class="{ 'rotate-12': unreadCount > 0 && !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span x-show="unreadCount > 0" x-cloak class="absolute -right-0.5 -top-0.5 inline-flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-neutral-900 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-white dark:bg-white dark:text-neutral-900 dark:ring-[#0d1117]" x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
    </button>

    <div x-show="open" 
         x-cloak 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
         data-slot="popover-content"
         class="fixed inset-x-2 top-16 z-50 w-auto overflow-hidden rounded-2xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#161b22] text-neutral-900 dark:text-[#e6edf3] shadow-xl ring-1 ring-black/5 dark:ring-white/5 sm:absolute sm:inset-x-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-[420px] outline-hidden origin-top-right">
        <div data-slot="popover-header" class="border-b border-neutral-100 bg-white px-4 pb-2 pt-4 dark:border-[#30363d] dark:bg-[#161b22] sm:px-6 sm:pt-5">
            <div class="flex items-center justify-between">
                <h3 data-slot="popover-title" class="text-base font-bold dark:text-white">Thông báo</h3>
                <button type="button"
                        x-show="unreadCount > 0"
                        x-cloak
                        @click="markAllAsRead()"
                        class="text-xs font-semibold text-neutral-400 transition-colors hover:text-neutral-900 dark:hover:text-white">
                    Đánh dấu đã đọc tất cả
                </button>
            </div>
            <div class="mt-3 flex gap-6">
                <button type="button" @click="filter = 'all'; showAll = false" class="relative py-2 text-sm font-bold" :class="filter === 'all' ? 'text-neutral-900 dark:text-white' : 'text-neutral-400'">
                    Tất cả
                    <span x-show="filter === 'all'" class="absolute inset-x-0 bottom-0 h-0.5 bg-neutral-900 dark:bg-white"></span>
                </button>
                <button type="button" @click="filter = 'unread'; showAll = false" class="relative py-2 text-sm font-semibold" :class="filter === 'unread' ? 'text-neutral-900 dark:text-white' : 'text-neutral-400'">
                    Chưa đọc
                    <span x-show="filter === 'unread'" class="absolute inset-x-0 bottom-0 h-0.5 bg-neutral-900 dark:bg-white"></span>
                </button>
            </div>
        </div>

        <div class="divide-y divide-neutral-100 dark:divide-[#30363d]"
             :class="{ 'max-h-[420px] overflow-y-auto overscroll-contain': showAll }">
            <template x-if="visibleNotifications.length === 0">
                <div class="px-6 py-12 text-center text-sm text-neutral-400">Hiện chưa có thông báo.</div>
            </template>

            <template x-for="item in visibleNotifications" :key="item.id">
                <div @click="markAsRead(item.id)"
                     @mouseenter="showDetails(item, $event)"
                     @mouseleave="hideDetails()"
                     class="relative flex cursor-pointer items-start gap-3 p-4 transition hover:bg-neutral-50 dark:hover:bg-[#21262d]/60 sm:gap-4 sm:p-6"
                     :class="{ 'bg-neutral-50/70 dark:bg-[#1a2333]/50': !isRead(item.id) }">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-neutral-100 text-neutral-900 dark:bg-[#21262d] dark:text-white"
                         :class="{
                             'bg-blue-100 text-blue-600 dark:bg-blue-950/50': item.type === 'system',
                             'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/50': item.type === 'course',
                             'bg-amber-100 text-amber-600 dark:bg-amber-950/50': item.type === 'promotion'
                         }">
                        <iconify-icon :icon="item.type === 'course' ? 'lucide:book-open' : item.type === 'promotion' ? 'lucide:tag' : 'lucide:rocket'" class="text-lg"></iconify-icon>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="mb-1 flex items-start justify-between gap-2">
                            <p class="min-w-0 break-words text-sm font-bold leading-tight dark:text-white" style="overflow-wrap: anywhere" x-text="item.title"></p>
                            <span x-show="!isRead(item.id)" class="mt-1 h-2 w-2 shrink-0 rounded-full bg-neutral-900 dark:bg-white"></span>
                        </div>
                        <p class="line-clamp-2 break-words text-[13px] leading-relaxed text-neutral-500 dark:text-[#8b949e]" style="overflow-wrap: anywhere" x-text="item.content"></p>
                        <span class="mt-3 block text-[11px] font-medium text-neutral-400 dark:text-[#7d8590]" x-text="item.time"></span>
                    </div>
                </div>
            </template>
        </div>
        <button type="button"
                x-show="showAll || (filter === 'all' ? notifications.length : notifications.filter(notification => !isRead(notification.id)).length) > 2"
                x-cloak
                @click="showAll = !showAll"
                class="w-full border-t border-neutral-100 px-5 py-3 text-center text-xs font-semibold text-neutral-500 transition hover:bg-neutral-50 hover:text-neutral-900 dark:border-[#30363d] dark:text-neutral-400 dark:hover:bg-[#21262d] dark:hover:text-white">
            <span x-text="showAll ? 'Thu gọn thông báo' : 'Xem tất cả thông báo'"></span>
        </button>
    </div>

    <template x-teleport="body">
        <div x-show="hoveredNotification"
             x-cloak
             x-transition
             :style="detailStyle"
             @mouseenter="clearTimeout(hideTimer)"
             @mouseleave="hideDetails()"
             class="fixed z-[80] hidden w-72 max-w-[calc(100vw-1rem)] overflow-hidden rounded-2xl border border-neutral-100 bg-white p-4 text-sm text-neutral-700 shadow-2xl dark:border-[#30363d] dark:bg-[#161b22] dark:text-[#e6edf3] sm:block">
            <p class="mb-1 break-words font-bold text-neutral-900 dark:text-white" style="overflow-wrap: anywhere" x-text="hoveredNotification?.title"></p>
            <div class="max-h-64 overflow-y-auto overscroll-contain pr-1" @wheel.stop>
                <p class="break-words leading-relaxed text-neutral-500 dark:text-[#8b949e]" style="overflow-wrap: anywhere" x-text="hoveredNotification?.content"></p>
            </div>
        </div>
    </template>
</div>
