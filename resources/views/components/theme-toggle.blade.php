{{-- Nút chuyển giao diện sáng/tối, lựa chọn được lưu vào localStorage --}}
<button type="button"
        x-data="{ dark: document.documentElement.classList.contains('dark') }"
        @click="dark = ! dark; document.documentElement.classList.toggle('dark', dark); try { localStorage.setItem('theme', dark ? 'dark' : 'light') } catch (e) {}"
        :aria-label="dark ? 'Chuyển sang giao diện sáng' : 'Chuyển sang giao diện tối'"
        :title="dark ? 'Giao diện sáng' : 'Giao diện tối'"
        {{ $attributes->merge(['class' => 'inline-flex h-9 w-9 items-center justify-center rounded-lg text-neutral-500 transition hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-white']) }}>
    <svg x-show="! dark" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
    <svg x-show="dark" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
</button>
