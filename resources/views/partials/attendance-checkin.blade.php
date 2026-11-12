<div class="relative" 
     data-slot="popover"
     x-data="{
    open: false,
    celebrating: false,
    loading: false,
    loaded: false,
    checkedIn: false,
    streak: 0,
    days: [],
    error: '',
    fireAnimation: null,
    async init() {
        await this.loadWeek();
    },
    async loadWeek() {
        try {
            const response = await fetch(@js(route('attendance.week')), {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Không thể tải lịch điểm danh.');
            }

            this.days = data.days;
            this.streak = data.streak;
            this.checkedIn = this.days.some(day => day.is_today && day.checked_in);
            this.loaded = true;
        } catch (error) {
            this.error = error.message || 'Không thể tải lịch điểm danh.';
        }
    },
    initFireAnimation() {
        if (this.fireAnimation || !window.lottie) {
            return;
        }

        const container = this.$refs.fireAnimation;

        if (!container) {
            return;
        }

        this.fireAnimation = window.lottie.loadAnimation({
            container,
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: @js(asset('animations/Fire.json'))
        });
    },
    async checkIn() {
        if (this.loading || this.checkedIn) {
            return;
        }

        this.loading = true;
        this.error = '';

        try {
            const response = await fetch(@js(route('attendance.check-in')), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Không thể điểm danh lúc này.');
            }

            this.checkedIn = true;
            this.streak = data.streak;
            const today = this.days.find(day => day.is_today);
            if (today) {
                today.checked_in = true;
            }

            if (!data.already_checked_in) {
                this.celebrating = true;
                this.$nextTick(() => this.initFireAnimation());
                window.setTimeout(() => this.celebrating = false, 2600);
                window.toast?.success(data.message || ('Điểm danh thành công! Chuỗi hiện tại: ' + data.streak + ' ngày.'), 'Điểm danh hằng ngày');
            } else {
                window.toast?.info('Hôm nay bạn đã điểm danh rồi!', 'Điểm danh hằng ngày');
            }
        } catch (error) {
            this.error = error.message || 'Không thể điểm danh lúc này.';
            window.toast?.error(this.error, 'Điểm danh thất bại');
        } finally {
            this.loading = false;
        }
    }
}" @click.outside="open = false" @keydown.escape.window="open = false; celebrating = false">
    <button type="button"
            @click="open = !open"
            data-slot="popover-trigger"
            :aria-expanded="open"
            aria-label="Điểm danh hôm nay"
            title="Điểm danh hôm nay"
            class="relative inline-flex h-9 w-9 items-center justify-center rounded-full text-[#1D1D1F] transition-colors hover:bg-black/5 focus:outline-none focus:ring-2 focus:ring-[#019D91] dark:text-[#e6edf3] dark:hover:bg-[#21262d]">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <rect x="3" y="4.5" width="18" height="16" rx="2" stroke-width="2"></rect>
            <path stroke-linecap="round" stroke-width="2" d="M8 2.5v4M16 2.5v4M3 9h18M8 13h.01M12 13h.01M16 13h.01M8 17h.01M12 17h.01"></path>
        </svg>
        <span x-show="!checkedIn" x-cloak class="absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full bg-[#019D91] ring-2 ring-white dark:ring-[#0d1117]"></span>
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
         class="absolute right-0 z-50 mt-2 w-[360px] max-w-[calc(100vw-2rem)] rounded-2xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#161b22] text-neutral-900 dark:text-[#e6edf3] p-5 shadow-xl ring-1 ring-black/5 dark:ring-white/5 outline-hidden origin-top-right">
        <div data-slot="popover-header" class="mb-6 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-500 dark:bg-orange-950/40">
                    <iconify-icon icon="lucide:flame" class="text-xl animate-pulse" aria-hidden="true"></iconify-icon>
                </div>
                <div>
                    <h3 data-slot="popover-title" class="text-sm font-bold leading-tight">Chuỗi điểm danh</h3>
                    <p data-slot="popover-description" class="mt-1 text-[11px] font-medium uppercase tracking-wider text-neutral-400">
                        <span x-text="streak"></span> ngày liên tiếp
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 rounded-full bg-neutral-900 px-3.5 py-2 text-[10px] font-bold uppercase tracking-tight text-white dark:bg-white dark:text-neutral-900">
                <iconify-icon icon="lucide:award" class="text-xs text-yellow-400 dark:text-yellow-500" aria-hidden="true"></iconify-icon>
                <span>Level <span x-text="Math.max(1, Math.ceil(streak / 5))"></span></span>
            </div>
        </div>

        <div class="mb-6 grid grid-cols-7 gap-1.5" aria-label="Lịch điểm danh trong tuần">
            <template x-for="day in days" :key="day.date">
                <div class="flex flex-col items-center rounded-xl border py-2.5"
                     :class="{
                         'border-orange-500 bg-orange-50 dark:bg-orange-950/30': day.checked_in,
                         'border-2 border-neutral-900 dark:border-white': day.is_today && !day.checked_in,
                         'border-neutral-100 bg-white dark:border-[#30363d] dark:bg-[#161b22]': !day.checked_in && !day.is_today
                     }">
                    <span class="mb-2 text-[9px] font-bold uppercase"
                          :class="day.is_today ? 'text-neutral-900 dark:text-white' : 'text-neutral-400'"
                          x-text="day.day"></span>
                    <div class="flex h-6 w-6 items-center justify-center">
                        <iconify-icon x-show="day.checked_in" icon="lucide:flame" class="text-lg text-orange-500" aria-hidden="true"></iconify-icon>
                        <span x-show="!day.checked_in" class="text-xs font-bold" :class="day.is_today ? 'text-neutral-800 dark:text-white' : 'text-neutral-400'" x-text="day.number"></span>
                    </div>
                </div>
            </template>
        </div>

        <button type="button" @click="checkIn()" :disabled="checkedIn || loading || !loaded"
                class="flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-neutral-900 text-sm font-bold text-white transition-all hover:bg-neutral-800 active:scale-[0.97] disabled:cursor-not-allowed disabled:opacity-60 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"></path>
            </svg>
            <span x-show="!loading && !checkedIn">Điểm danh hôm nay</span>
            <span x-show="loading">Đang điểm danh...</span>
            <span x-show="!loading && checkedIn">Đã điểm danh hôm nay</span>
        </button>
        <p x-show="error" x-text="error" class="mt-2 text-xs text-rose-600 dark:text-rose-400"></p>
    </div>

    <template x-teleport="body">
        <div x-show="celebrating" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/25 px-4 backdrop-blur-[2px]" role="status" aria-live="polite">
            <div x-transition.scale class="flex flex-col items-center text-center">
                <div class="relative h-36 w-36">
                    <div x-ref="fireAnimation" class="h-full w-full" aria-label="Hiệu ứng lửa giữ chuỗi"></div>
                </div>
                <p class="mt-5 text-3xl font-extrabold tracking-tight text-white drop-shadow-lg">x<span x-text="streak"></span> streak</p>
                <p class="mt-1 text-sm font-semibold text-white/90">Giữ vững chuỗi học tập nhé!</p>
            </div>
        </div>
    </template>
</div>
