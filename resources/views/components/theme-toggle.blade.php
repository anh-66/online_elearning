{{-- Nút chuyển giao diện sáng/tối, đồng bộ tức thì với cài đặt người dùng và lưu vào localStorage --}}
<button type="button"
        x-data="{
            dark: document.documentElement.classList.contains('dark'),
            init() {
                this.dark = document.documentElement.classList.contains('dark');
                const observer = new MutationObserver(() => {
                    this.dark = document.documentElement.classList.contains('dark');
                });
                observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
            },
            toggle() {
                const nextDark = !document.documentElement.classList.contains('dark');
                this.dark = nextDark;
                document.documentElement.classList.toggle('dark', nextDark);
                const nextMode = nextDark ? 'dark' : 'light';
                try {
                    localStorage.setItem('theme', nextMode);
                } catch (e) {}
                window.dispatchEvent(new CustomEvent('theme-changed', {
                    detail: { theme: nextMode, dark: nextDark }
                }));
            }
        }"
        @click="toggle()"
        @theme-changed.window="dark = $event.detail.dark"
        :aria-label="dark ? 'Chuyển sang giao diện sáng' : 'Chuyển sang giao diện tối'"
        :title="dark ? 'Chế độ tối (Bấm để chuyển sang sáng)' : 'Chế độ sáng (Bấm để chuyển sang tối)'"
        {{ $attributes->merge(['class' => 'inline-flex h-9 w-9 items-center justify-center rounded-lg text-neutral-500 transition hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-white']) }}>
    {{-- Khi ở chế độ sáng: hiển thị icon mặt trời --}}
    <svg x-show="! dark" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
    </svg>
    {{-- Khi ở chế độ tối: hiển thị icon mặt trăng --}}
    <svg x-show="dark" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
    </svg>
</button>
