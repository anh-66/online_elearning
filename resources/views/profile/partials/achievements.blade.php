<script>
(function() {
    const registerAchievementsManager = () => {
        if (!window.Alpine) return;
        window.Alpine.data('achievementsManager', (config = {}) => ({
            filterCategory: 'all',
            filterStatus: 'all',
            selectedAchievement: null,
            currentPage: 1,
            perPage: 9,
            achievements: config.achievements || [],
            totalPointsEarned: config.totalPointsEarned || 0,
            celebrating: false,
            celebratedAt: 0,
            claimedItem: null,
            trophyAnimation: null,
            trophyAnimationData: null,

            async loadTrophyData() {
                if (this.trophyAnimationData) return this.trophyAnimationData;
                try {
                    const res = await fetch(@js(asset('animations/Trophy.json')));
                    this.trophyAnimationData = await res.json();
                } catch(e) {
                    console.error('Không tải được animation Trophy:', e);
                }
                return this.trophyAnimationData;
            },

            init() {
                // Tải trước animation vào bộ nhớ để chạy 60 FPS mượt mà
                this.loadTrophyData();

                const pending = config.pendingNotifications || [];
                if (Array.isArray(pending) && pending.length > 0) {
                    setTimeout(() => {
                        pending.forEach(item => {
                            window.toast?.success(
                                'Bạn vừa hoàn thành mục tiêu: ' + item.name + '! Nhấn "Nhận ngay" để nhận +' + item.points + ' XP.',
                                'Mở khóa thành tựu!',
                                {
                                    label: 'Nhận ngay',
                                    onClick: () => {
                                        this.claim(item.id);
                                    }
                                }
                            );
                        });
                    }, 500);
                }
            },

            openModal(item) {
                this.selectedAchievement = item;
            },
            closeModal() {
                this.selectedAchievement = null;
            },

            async initTrophyAnimation() {
                if (this.trophyAnimation) {
                    try {
                        this.trophyAnimation.destroy();
                    } catch (e) {}
                    this.trophyAnimation = null;
                }

                const container = document.getElementById('trophy-lottie-container');
                if (!container || !window.lottie) return;

                container.innerHTML = '';

                const animData = await this.loadTrophyData();
                if (!animData) return;

                try {
                    this.trophyAnimation = window.lottie.loadAnimation({
                        container: container,
                        renderer: 'svg',
                        loop: true,
                        autoplay: true,
                        animationData: animData
                    });
                } catch (e) {
                    console.error('Lỗi khởi tạo Lottie:', e);
                }
            },

            dismissCelebration() {
                // Ngăn đóng nhầm do click ban đầu (bảo vệ 350ms)
                if (Date.now() - this.celebratedAt < 350) return;

                this.celebrating = false;
                if (this.trophyAnimation) {
                    try {
                        this.trophyAnimation.stop();
                        this.trophyAnimation.destroy();
                    } catch (e) {}
                    this.trophyAnimation = null;
                }
            },

            async claim(idOrItem) {
                const id = (typeof idOrItem === 'object' && idOrItem !== null) ? idOrItem.id : idOrItem;
                if (!id) return;

                const target = this.achievements.find(a => Number(a.id) === Number(id));
                if (!target || !target.can_claim || target.is_claiming) return;

                target.is_claiming = true;

                try {
                    const response = await fetch(`/profile/achievements/${id}/claim`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Không thể nhận thưởng lúc này.');
                    }

                    // Đóng modal chi tiết nếu đang mở
                    this.closeModal();

                    // Cập nhật trạng thái item trong toàn bộ danh sách
                    target.can_claim = false;
                    target.is_claimed = true;
                    target.claimed_at = data.achievement.claimed_at;
                    target.is_unlocked = true;

                    // Tăng điểm XP tức thì trên thẻ tổng quan
                    this.totalPointsEarned += target.points;

                    // Bật màn hình chúc mừng Trophy.json
                    this.celebratedAt = Date.now();
                    this.claimedItem = { ...target };
                    this.celebrating = true;
                    this.$nextTick(() => {
                        this.initTrophyAnimation();
                    });

                    window.toast?.success(`Bạn đã nhận thành công +${target.points} XP từ thành tựu: ${target.name}!`, 'Đã nhận thưởng');
                } catch (err) {
                    window.toast?.error(err.message || 'Không thể nhận thưởng.', 'Lỗi');
                } finally {
                    target.is_claiming = false;
                }
            },

            matchesFilter(item) {
                const matchCat = (this.filterCategory === 'all') || (item.category === this.filterCategory);
                let matchStatus = true;
                if (this.filterStatus === 'ready') {
                    matchStatus = item.can_claim;
                } else if (this.filterStatus === 'unlocked') {
                    matchStatus = item.is_claimed;
                } else if (this.filterStatus === 'in_progress') {
                    matchStatus = !item.is_unlocked && item.progress > 0;
                } else if (this.filterStatus === 'locked') {
                    matchStatus = !item.is_unlocked && item.progress === 0;
                }
                return matchCat && matchStatus;
            },

            get filteredAchievements() {
                return this.achievements.filter(a => this.matchesFilter(a));
            },
            get totalPages() {
                return Math.max(1, Math.ceil(this.filteredAchievements.length / this.perPage));
            },
            get visibleAchievements() {
                const start = (this.currentPage - 1) * this.perPage;
                return this.filteredAchievements.slice(start, start + this.perPage);
            },
            get paginationPages() {
                const total = this.totalPages;
                const current = this.currentPage;
                if (total <= 7) {
                    return Array.from({ length: total }, (_, i) => i + 1);
                }
                if (current <= 4) {
                    return [1, 2, 3, 4, 5, '...', total];
                }
                if (current >= total - 3) {
                    return [1, '...', total - 4, total - 3, total - 2, total - 1, total];
                }
                return [1, '...', current - 1, current, current + 1, '...', total];
            },
            goToPage(page) {
                if (page === '...' || page < 1 || page > this.totalPages) return;
                this.currentPage = page;
                this.$nextTick(() => {
                    const el = document.getElementById('achievements-grid-header');
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            },
            getTierBadge(tier) {
                switch(tier) {
                    case 'bronze':
                        return 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border-gray-200 dark:border-neutral-700';
                    case 'silver':
                        return 'bg-slate-50 text-slate-700 dark:bg-slate-800/60 dark:text-slate-300 border-gray-200 dark:border-neutral-700';
                    case 'gold':
                        return 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border-gray-200 dark:border-neutral-700';
                    case 'platinum':
                        return 'bg-cyan-50 text-cyan-700 dark:bg-cyan-950/40 dark:text-cyan-300 border-gray-200 dark:border-neutral-700';
                    case 'diamond':
                        return 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border-gray-200 dark:border-neutral-700';
                    default:
                        return 'bg-gray-50 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border-gray-200 dark:border-neutral-700';
                }
            }
        }));
    };

    if (window.Alpine) {
        registerAchievementsManager();
    } else {
        document.addEventListener('alpine:init', registerAchievementsManager);
    }
})();
</script>

<div x-data="achievementsManager({
    achievements: @js($achievementsData['achievements'] ?? []),
    totalPointsEarned: @js($achievementsData['total_points_earned'] ?? 0),
    pendingNotifications: @js($achievementsData['pending_notifications'] ?? [])
})" class="space-y-8">

    <!-- 1. ACHIEVEMENT OVERVIEW CARD -->
    <div class="rounded-2xl border border-gray-200/80 bg-white p-6 sm:p-8 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2 max-w-xl">
                <h3 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Hành trình Chinh phục Tri thức
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                    Mỗi cột mốc đều đánh dấu sự kiên trì và nỗ lực của bạn. Hoàn thành các bài học, đạt chuỗi điểm danh và vượt qua thử thách để tích lũy danh hiệu độc quyền.
                </p>
            </div>

            <!-- Stats badges right -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 shrink-0">
                <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-3.5 text-center dark:border-neutral-800 dark:bg-neutral-800/50">
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Đã hoàn thành</div>
                    <div class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                        {{ $achievementsData['unlocked_count'] ?? 0 }}<span class="text-xs font-normal text-gray-400 dark:text-gray-500">/{{ $achievementsData['total_achievements'] ?? 0 }}</span>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-100 bg-gray-50/70 p-3.5 text-center dark:border-neutral-800 dark:bg-neutral-800/50">
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Tỷ lệ hoàn thành</div>
                    <div class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                        {{ $achievementsData['completion_percentage'] ?? 0 }}%
                    </div>
                </div>

                <div class="col-span-2 sm:col-span-1 rounded-xl border border-gray-100 bg-gray-50/70 p-3.5 text-center dark:border-neutral-800 dark:bg-neutral-800/50">
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Tổng điểm XP</div>
                    <div class="mt-1 text-xl font-bold text-blue-600 dark:text-blue-400">
                        <span x-text="Number(totalPointsEarned).toLocaleString('vi-VN')"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overall Progress bar -->
        <div class="relative z-10 mt-6 pt-6 border-t border-gray-100 dark:border-neutral-800/80 space-y-2">
            <div class="flex items-center justify-between text-xs font-medium">
                <span class="text-gray-600 dark:text-gray-300">Tiến trình tổng quan</span>
                <span class="text-gray-500 dark:text-gray-400 font-semibold">
                    Còn {{ $achievementsData['locked_count'] ?? 0 }} thành tựu chưa mở khóa
                </span>
            </div>
            <div class="h-2.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-neutral-800">
                <div class="h-full rounded-full bg-blue-600 dark:bg-blue-500 transition-all duration-700 ease-out"
                     style="width: {{ $achievementsData['completion_percentage'] ?? 0 }}%;"></div>
            </div>
        </div>
    </div>

    <!-- 2. FILTER & CATEGORY TABS -->
    <div id="achievements-grid-header" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 pb-4 dark:border-neutral-800">
        <!-- Categories tabs -->
        <div class="flex flex-wrap items-center gap-1.5">
            <template x-for="(label, catKey) in {
                'all': 'Tất cả',
                'learning': 'Học tập',
                'streak': 'Chuyên cần',
                'quiz': 'Quiz & Đố vui',
                'community': 'Cộng đồng'
            }" :key="catKey">
                <button type="button"
                        @click="filterCategory = catKey; currentPage = 1"
                        :class="filterCategory === catKey
                            ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900 font-medium shadow-xs'
                            : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-neutral-800'"
                        class="rounded-lg px-3.5 py-1.5 text-xs transition-colors">
                    <span x-text="label"></span>
                </button>
            </template>
        </div>

        <!-- Status filter select -->
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400 dark:text-gray-500">Trạng thái:</span>
            <select x-model="filterStatus"
                    @change="currentPage = 1"
                    class="rounded-lg border-gray-200 bg-white py-1.5 pl-3 pr-8 text-xs text-gray-700 shadow-2xs focus:border-blue-500 focus:ring-blue-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-gray-200">
                <option value="all">Tất cả trạng thái</option>
                <option value="ready">Chờ nhận thưởng</option>
                <option value="unlocked">Đã hoàn thành</option>
                <option value="in_progress">Đang thực hiện</option>
                <option value="locked">Chưa mở khóa</option>
            </select>
        </div>
    </div>

    <!-- 3. ACHIEVEMENT GRID -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <template x-for="item in visibleAchievements" :key="item.id">
            <div x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 :class="(!item.is_unlocked && item.progress === 0) ? 'opacity-70 hover:opacity-100' : ''"
                 class="group relative flex flex-col justify-between rounded-xl border border-gray-200/90 bg-white p-5 transition-all hover:border-gray-300 hover:shadow-xs dark:border-neutral-800 dark:bg-neutral-900 dark:hover:border-neutral-700">
                
                <!-- Phần thân thẻ: Click vào để mở Modal xem chi tiết -->
                <div @click="openModal(item)" class="cursor-pointer">
                    <!-- Card Top: Badge Icon + Tier Label -->
                    <div class="flex items-start justify-between gap-3 mb-3.5">
                        <!-- Icon Trophy Avatar (Không viền màu nền xung quanh) -->
                        <div class="relative shrink-0 flex h-9 w-9 items-center justify-center">
                            <iconify-icon :icon="item.icon"
                                          :class="{
                                              'text-amber-500 dark:text-amber-400': item.is_unlocked,
                                              'text-blue-500 dark:text-blue-400': !item.is_unlocked && item.progress > 0,
                                              'text-gray-300 dark:text-neutral-600': !item.is_unlocked && item.progress === 0
                                          }"
                                          class="text-3xl transition-transform group-hover:scale-110"></iconify-icon>

                            <!-- Status indicator pin on icon corner -->
                            <div x-show="item.is_claimed"
                                 class="absolute -bottom-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-emerald-500 text-white shadow-xs">
                                <iconify-icon icon="lucide:check" class="text-[10px] font-bold"></iconify-icon>
                            </div>
                            <div x-show="item.can_claim"
                                 class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-neutral-900 shadow-xs"
                                 title="Sẵn sàng nhận thưởng"></div>
                            <div x-show="!item.is_unlocked"
                                 class="absolute -bottom-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-gray-400 text-white dark:bg-neutral-700 shadow-xs">
                                <iconify-icon icon="lucide:lock" class="text-[9px]"></iconify-icon>
                            </div>
                        </div>

                        <!-- Tier badge -->
                        <div class="flex items-center gap-1.5">
                            <span :class="getTierBadge(item.tier)"
                                  class="inline-flex items-center gap-1 rounded-md border px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wider">
                                <span x-text="item.tier_label"></span>
                            </span>
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400" x-text="'+' + item.points + ' XP'"></span>
                        </div>
                    </div>

                    <!-- Title & Description -->
                    <div class="space-y-1">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"
                            x-text="item.name"></h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed"
                            x-text="item.description"></p>
                    </div>
                </div>

                <!-- Card Bottom: Actions / Progress / Status (Tách biệt hoàn toàn, nút Nhận ngay click trực tiếp) -->
                <div class="mt-4 pt-3.5 border-t border-gray-100 dark:border-neutral-800/80 space-y-2">
                    <!-- TH 1: ĐÃ ĐẠT ĐIỀU KIỆN NHƯNG CHƯA NHẬN THƯỞNG -> Nút "Nhận ngay" -->
                    <button type="button"
                            x-show="item.can_claim"
                            @click="claim(item.id)"
                            :disabled="item.is_claiming"
                            class="w-full flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-2 px-3 shadow-xs active:scale-95 transition-all cursor-pointer">
                        <iconify-icon icon="lucide:gift" class="text-sm pointer-events-none"></iconify-icon>
                        <span class="pointer-events-none" x-text="item.is_claiming ? 'Đang nhận...' : 'Nhận ngay +' + item.points + ' XP'"></span>
                    </button>

                    <!-- TH 2: ĐÃ NHẬN THƯỞNG HOÀN TẤT -->
                    <div x-show="item.is_claimed" class="flex items-center justify-between text-[11px] font-medium text-emerald-600 dark:text-emerald-400">
                        <span class="inline-flex items-center gap-1">
                            <iconify-icon icon="lucide:check-circle" class="text-xs"></iconify-icon>
                            <span>Đã hoàn thành</span>
                        </span>
                        <span class="text-gray-400 dark:text-gray-500"
                              x-text="item.claimed_at ? new Date(item.claimed_at).toLocaleDateString('vi-VN') : (item.unlocked_at ? new Date(item.unlocked_at).toLocaleDateString('vi-VN') : 'Đã đạt')"></span>
                    </div>

                    <!-- TH 3: CHƯA ĐẠT MỤC TIÊU -> THANH TIẾN ĐỘ -->
                    <div x-show="!item.is_unlocked" class="space-y-1.5">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-gray-500 dark:text-gray-400 font-medium">Tiến độ</span>
                            <span class="font-semibold text-gray-700 dark:text-gray-300"
                                  x-text="item.progress + ' / ' + item.criteria_goal"></span>
                        </div>
                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-neutral-800">
                            <div class="h-full rounded-full bg-blue-600 dark:bg-blue-500 transition-all duration-500"
                                 :style="'width: ' + item.percentage + '%;'"></div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- 4. PHÂN TRANG -->
    <div x-show="totalPages > 1" class="mt-8 flex justify-center">
        <nav role="navigation" aria-label="Phân trang thành tựu" class="mx-auto flex w-full justify-center select-none"
             x-data="{
                 hover: { left: '0px', top: '0px', width: '0px', height: '0px', opacity: 0 },
                 setHover(el) {
                     if (!el) return;
                     this.hover = {
                         left: el.offsetLeft + 'px',
                         top: el.offsetTop + 'px',
                         width: el.offsetWidth + 'px',
                         height: el.offsetHeight + 'px',
                         opacity: 1
                     };
                 },
                 clearHover() {
                     this.hover.opacity = 0;
                 }
             }">
            <ul class="relative flex items-center gap-1 p-1 rounded-xl" @mouseleave="clearHover()">
                <!-- Smooth Gliding Hover Pill -->
                <div class="absolute rounded-lg bg-black/[0.06] dark:bg-white/15 pointer-events-none transition-all duration-250 ease-[cubic-bezier(0.22,1,0.36,1)] z-0"
                     :style="`left: ${hover.left}; top: ${hover.top}; width: ${hover.width}; height: ${hover.height}; opacity: ${hover.opacity};`"
                     x-cloak>
                </div>

                <!-- Nút Trước -->
                <li>
                    <button type="button"
                            @click="goToPage(currentPage - 1)"
                            :disabled="currentPage === 1"
                            :class="currentPage === 1
                                ? 'text-neutral-300 dark:text-neutral-600 cursor-not-allowed opacity-50'
                                : 'text-neutral-700 dark:text-neutral-300 hover:text-neutral-950 dark:hover:text-white cursor-pointer active:scale-95'"
                            @mouseenter="currentPage > 1 && setHover($el)"
                            class="relative z-10 h-9 pl-2 pr-3 gap-1.5 text-sm font-medium rounded-lg inline-flex items-center justify-center transition-colors">
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span class="hidden sm:inline">Trước</span>
                    </button>
                </li>

                <!-- Danh sách trang -->
                <template x-for="(page, idx) in paginationPages" :key="idx">
                    <li>
                        <template x-if="page === '...'">
                            <span class="size-9 flex items-center justify-center text-neutral-400 dark:text-neutral-500">
                                <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="9"/>
                                    <circle cx="8" cy="12" r="0.75" fill="currentColor"/>
                                    <circle cx="12" cy="12" r="0.75" fill="currentColor"/>
                                    <circle cx="16" cy="12" r="0.75" fill="currentColor"/>
                                </svg>
                            </span>
                        </template>
                        <template x-if="page !== '...'">
                            <button type="button"
                                    @click="goToPage(page)"
                                    @mouseenter="page !== currentPage && setHover($el)"
                                    :class="page === currentPage
                                        ? 'bg-neutral-900 text-white dark:bg-white dark:text-neutral-950 font-bold shadow-xs'
                                        : 'text-neutral-600 dark:text-neutral-400 hover:text-neutral-950 dark:hover:text-white font-medium active:scale-95'"
                                    class="relative z-10 size-9 inline-flex items-center justify-center rounded-lg text-sm transition-all select-none"
                                    x-text="page">
                            </button>
                        </template>
                    </li>
                </template>

                <!-- Nút Sau -->
                <li>
                    <button type="button"
                            @click="goToPage(currentPage + 1)"
                            :disabled="currentPage === totalPages"
                            :class="currentPage === totalPages
                                ? 'text-neutral-300 dark:text-neutral-600 cursor-not-allowed opacity-50'
                                : 'text-neutral-700 dark:text-neutral-300 hover:text-neutral-950 dark:hover:text-white cursor-pointer active:scale-95'"
                            @mouseenter="currentPage < totalPages && setHover($el)"
                            class="relative z-10 h-9 pl-3 pr-2 gap-1.5 text-sm font-medium rounded-lg inline-flex items-center justify-center transition-colors">
                        <span class="hidden sm:inline">Sau</span>
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Empty state when filter has no results -->
    <div x-show="filteredAchievements.length === 0"
         class="py-12 text-center rounded-xl border border-dashed border-gray-200 dark:border-neutral-800">
        <iconify-icon icon="lucide:award-off" class="text-4xl text-gray-300 dark:text-neutral-600 mb-2"></iconify-icon>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Không tìm thấy thành tựu nào phù hợp bộ lọc.</p>
        <button type="button" @click="filterCategory = 'all'; filterStatus = 'all'"
                class="mt-3 text-xs font-semibold text-blue-600 hover:underline dark:text-blue-400">
            Đặt lại bộ lọc
        </button>
    </div>

    <!-- 5. MODAL CHI TIẾT HUY HIỆU -->
    <template x-teleport="body">
        <div x-show="selectedAchievement !== null"
             x-cloak
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto"
             @keydown.escape.window="closeModal()">

            <div x-show="selectedAchievement !== null"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                 @click.away="closeModal()"
                 class="relative my-auto w-full max-w-md overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-2xl dark:border-neutral-800 dark:bg-neutral-900 text-center">

                <!-- Close button -->
                <button type="button" @click="closeModal()"
                        class="absolute top-4 right-4 rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-neutral-800 dark:hover:text-gray-300">
                    <iconify-icon icon="lucide:x" class="text-lg"></iconify-icon>
                </button>

                <!-- Big Avatar Badge (Không viền màu nền) -->
                <div class="mx-auto mt-2 mb-4 flex items-center justify-center">
                    <iconify-icon :icon="selectedAchievement?.icon || 'lucide:trophy'"
                                  :class="selectedAchievement?.is_unlocked ? 'text-amber-500 dark:text-amber-400' : 'text-gray-300 dark:text-neutral-600'"
                                  class="text-5xl transition-all"></iconify-icon>
                </div>

                <!-- Tier & Category -->
                <div class="flex items-center justify-center gap-2 mb-2">
                    <span :class="getTierBadge(selectedAchievement?.tier)"
                          class="inline-flex items-center gap-1 rounded-md border px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wider">
                        <span x-text="selectedAchievement?.tier_label"></span>
                    </span>
                    <span class="rounded-md bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-neutral-800 dark:text-gray-300"
                          x-text="selectedAchievement?.category_label"></span>
                </div>

                <!-- Name & Description -->
                <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="selectedAchievement?.name"></h3>
                <p class="mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed"
                   x-text="selectedAchievement?.description"></p>

                <!-- Rewards & Progress details box -->
                <div class="mt-6 rounded-xl border border-gray-100 bg-gray-50/70 p-4 text-left dark:border-neutral-800 dark:bg-neutral-800/40 space-y-3">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500 dark:text-gray-400">Phần thưởng kinh nghiệm:</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400" x-text="'+' + selectedAchievement?.points + ' XP'"></span>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500 dark:text-gray-400">Trạng thái:</span>
                        <span :class="{
                                  'text-blue-600 dark:text-blue-400 font-bold': selectedAchievement?.can_claim,
                                  'text-emerald-600 dark:text-emerald-400 font-semibold': selectedAchievement?.is_claimed,
                                  'text-gray-600 dark:text-gray-400': !selectedAchievement?.is_unlocked
                              }"
                              x-text="selectedAchievement?.can_claim ? 'Đã hoàn thành - Sẵn sàng nhận thưởng!' : (selectedAchievement?.is_claimed ? 'Đã hoàn thành và nhận thưởng' : 'Đang thực hiện')"></span>
                    </div>

                    <div class="space-y-1 pt-1 border-t border-gray-200/50 dark:border-neutral-700/50">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 dark:text-gray-400">Tiến trình đạt được:</span>
                            <span class="font-semibold text-gray-900 dark:text-white"
                                  x-text="(selectedAchievement?.progress || 0) + ' / ' + (selectedAchievement?.criteria_goal || 1)"></span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-neutral-700">
                            <div class="h-full rounded-full transition-all duration-500"
                                 :class="selectedAchievement?.is_unlocked ? 'bg-blue-600' : 'bg-blue-600'"
                                 :style="'width: ' + (selectedAchievement?.percentage || 0) + '%;'"></div>
                        </div>
                    </div>

                    <template x-if="selectedAchievement?.is_claimed && (selectedAchievement?.claimed_at || selectedAchievement?.unlocked_at)">
                        <div class="text-[11px] text-gray-400 dark:text-gray-500 text-center pt-1">
                            Hoàn thành vào ngày <span class="font-medium text-gray-600 dark:text-gray-300" x-text="new Date(selectedAchievement.claimed_at || selectedAchievement.unlocked_at).toLocaleString('vi-VN')"></span>
                        </div>
                    </template>
                </div>

                <!-- Modal Action buttons -->
                <div class="mt-6 flex flex-col gap-2">
                    <template x-if="selectedAchievement?.can_claim">
                        <button type="button" @click="claim(selectedAchievement.id)"
                                class="w-full flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 px-4 py-2.5 text-xs font-bold text-white transition-all shadow-sm active:scale-95 cursor-pointer">
                            <iconify-icon icon="lucide:gift" class="text-sm"></iconify-icon>
                            <span x-text="'Nhận ngay +' + selectedAchievement?.points + ' XP'"></span>
                        </button>
                    </template>
                    <button type="button" @click="closeModal()"
                            class="w-full rounded-xl bg-gray-900 px-4 py-2.5 text-xs font-semibold text-white transition-all hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 cursor-pointer">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- 6. HIỆU ỨNG CHÚC MỪNG MỞ KHÓA THÀNH TỰU (Lottie Trophy.json - 60 FPS MƯỢT MÀ) -->
    <template x-teleport="body">
        <div x-show="celebrating"
             x-cloak
             @click="dismissCelebration()"
             @keydown.escape.window="dismissCelebration()"
             class="fixed inset-0 z-[150] flex items-center justify-center bg-black/80 px-4 cursor-pointer select-none"
             role="dialog"
             aria-modal="true">
            <div x-show="celebrating"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90"
                 class="flex flex-col items-center text-center max-w-sm p-6 pointer-events-auto">
                
                <!-- Lottie Trophy Animation Container (Kích thước chuẩn, không giật lag) -->
                <div class="relative h-44 w-44 sm:h-52 sm:w-52 flex items-center justify-center">
                    <div id="trophy-lottie-container" class="h-full w-full"></div>
                </div>

                <!-- Congratulations Info -->
                <div class="mt-3 space-y-1.5">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/20 px-3 py-1 text-xs font-bold text-amber-400 border border-amber-500/30">
                        <iconify-icon icon="lucide:sparkles" class="text-sm"></iconify-icon>
                        <span>MỞ KHÓA THÀNH TỰU</span>
                    </span>
                    <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight drop-shadow-md" x-text="claimedItem?.name"></h3>
                    <p class="text-sm text-gray-200 font-medium" x-text="'+' + (claimedItem?.points || 0) + ' XP đã được cộng vào tài khoản!'"></p>
                    <p class="text-[11px] text-gray-400 pt-2">Nhấn bất kỳ vị trí nào để đóng</p>
                </div>
            </div>
        </div>
    </template>
</div>
