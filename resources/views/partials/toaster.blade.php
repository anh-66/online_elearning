{{-- Animate UI Toaster Component (@animate-ui/react/primitives/base-ui/toast) --}}
<style>
@keyframes toastProgressBarAnim {
    0% { transform: scaleX(1); }
    100% { transform: scaleX(0); }
}
.toast-progress-bar {
    transform-origin: left;
    animation-name: toastProgressBarAnim;
    animation-timing-function: linear;
    animation-fill-mode: forwards;
}
</style>

<script>
    if (typeof window !== 'undefined' && !window.toast) {
        window.toast = {
            show(detail) {
                window.dispatchEvent(new CustomEvent('toast', { detail }));
            },
            success(message, title = 'Thành công', action = null) {
                this.show({ type: 'success', title, description: message, action });
            },
            error(message, title = 'Có lỗi xảy ra', action = null) {
                this.show({ type: 'error', title, description: message, action });
            },
            info(message, title = 'Thông báo', action = null) {
                this.show({ type: 'info', title, description: message, action });
            },
            warning(message, title = 'Cảnh báo', action = null) {
                this.show({ type: 'warning', title, description: message, action });
            },
            loading(message, title = 'Đang xử lý...') {
                this.show({ type: 'loading', title, description: message, duration: 8000 });
            }
        };
    }
</script>

<div data-slot="toast-viewport"
     x-data="animateUiToast()"
     @toast.window="addToast($event.detail)"
     class="pointer-events-none fixed bottom-6 right-6 z-[99999] flex flex-col gap-3 w-[calc(100vw-3rem)] max-w-sm sm:max-w-[400px] outline-none select-none">
     
    <template x-for="t in toasts" :key="t.id">
        <div data-slot="toast"
             x-show="t.visible"
             @mouseenter="pause(t)"
             @mouseleave="resume(t)"
             x-transition:enter="transition-all ease-[cubic-bezier(0.16,1,0.3,1)] duration-300"
             x-transition:enter-start="opacity-0 translate-y-3 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition-all ease-[cubic-bezier(0.16,1,0.3,1)] duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
             class="group/toast pointer-events-auto relative w-full rounded-2xl border border-neutral-200/90 dark:border-neutral-800 bg-white/95 dark:bg-neutral-900/95 backdrop-blur-md text-neutral-900 dark:text-neutral-100 shadow-[0_12px_32px_-4px_rgba(0,0,0,0.12),0_4px_12px_rgba(0,0,0,0.06)] dark:shadow-[0_12px_32px_-4px_rgba(0,0,0,0.6)] overflow-hidden transition-all">
            
            <div data-slot="toast-content" class="flex items-center gap-3.5 p-4">
                
                {{-- Toast Icon Badge with rounded border & soft background --}}
                <div data-slot="toast-icon" class="shrink-0 flex items-center justify-center">
                    <template x-if="t.type === 'success'">
                        <span class="size-8 rounded-full aspect-square flex items-center justify-center bg-emerald-50 text-emerald-600 border border-emerald-200/80 dark:bg-emerald-950/40 dark:border-emerald-800/60 dark:text-emerald-400">
                            <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </span>
                    </template>
                    <template x-if="t.type === 'error'">
                        <span class="size-8 rounded-full aspect-square flex items-center justify-center bg-rose-50 text-rose-600 border border-rose-200/80 dark:bg-rose-950/40 dark:border-rose-800/60 dark:text-rose-400">
                            <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                            </svg>
                        </span>
                    </template>
                    <template x-if="t.type === 'warning'">
                        <span class="size-8 rounded-full aspect-square flex items-center justify-center bg-amber-50 text-amber-600 border border-amber-200/80 dark:bg-amber-950/40 dark:border-amber-800/60 dark:text-amber-400">
                            <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                        </span>
                    </template>
                    <template x-if="t.type === 'info'">
                        <span class="size-8 rounded-full aspect-square flex items-center justify-center bg-sky-50 text-sky-600 border border-sky-200/80 dark:bg-sky-950/40 dark:border-sky-800/60 dark:text-sky-400">
                            <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                            </svg>
                        </span>
                    </template>
                    <template x-if="t.type === 'loading'">
                        <span class="size-8 rounded-full aspect-square flex items-center justify-center bg-neutral-100 text-neutral-600 border border-neutral-200 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400">
                            <svg class="size-4 shrink-0 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                            </svg>
                        </span>
                    </template>
                </div>

                {{-- Toast Message (Title & Description) --}}
                <div class="flex-1 min-w-0 pr-1 flex flex-col gap-0.5">
                    <p data-slot="toast-title" 
                       x-text="t.title" 
                       class="text-sm font-semibold text-neutral-900 dark:text-white leading-tight">
                    </p>
                    <p data-slot="toast-description" 
                       x-show="t.description" 
                       x-text="t.description" 
                       class="text-xs sm:text-sm text-neutral-500 dark:text-neutral-400 leading-normal">
                    </p>
                </div>

                {{-- Optional Action Button --}}
                <template x-if="t.action">
                    <button type="button"
                            @click="t.action.onClick ? t.action.onClick() : null; close(t.id)"
                            data-slot="toast-action"
                            x-text="t.action.label"
                            class="shrink-0 self-center px-2.5 py-1 text-xs font-semibold rounded-lg border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-neutral-800 dark:text-neutral-200 hover:bg-neutral-50 dark:hover:bg-neutral-700 transition-colors shadow-2xs">
                    </button>
                </template>

                {{-- Close Button --}}
                <button type="button"
                        @click="close(t.id)"
                        data-slot="toast-close"
                        class="shrink-0 -mr-1 p-1.5 rounded-lg text-neutral-400 hover:text-neutral-700 dark:hover:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors"
                        aria-label="Đóng thông báo">
                    <svg class="size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>
            
            {{-- Progress bar: pauses on hover, resumes on leave, smoothly shrinks to 0 --}}
            <div class="h-1 w-full bg-neutral-100 dark:bg-neutral-800/80 overflow-hidden">
                <div class="toast-progress-bar h-full w-full"
                     :class="{
                         'bg-emerald-500': t.type === 'success',
                         'bg-rose-500': t.type === 'error',
                         'bg-amber-500': t.type === 'warning',
                         'bg-sky-500': t.type === 'info',
                         'bg-neutral-800 dark:bg-neutral-200': !t.type || t.type === 'loading'
                     }"
                     :style="`animation-duration: ${t.duration}ms; animation-play-state: ${t.isPaused ? 'paused' : 'running'};`">
                </div>
            </div>
        </div>
    </template>
</div>

@if(session('success') || session('error') || session('warning') || session('info'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            window.toast?.success(@js(session('success')));
        @endif
        @if(session('error'))
            window.toast?.error(@js(session('error')));
        @endif
        @if(session('warning'))
            window.toast?.warning(@js(session('warning')));
        @endif
        @if(session('info'))
            window.toast?.info(@js(session('info')));
        @endif
    });
</script>
@endif
