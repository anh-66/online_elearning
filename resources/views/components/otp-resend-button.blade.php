@props(['seconds' => 0])

{{-- Nút "Gửi lại mã" có đếm ngược, dùng bên trong một form POST. --}}
<button
    type="submit"
    x-data="{
        left: {{ max(0, (int) $seconds) }},
        init() {
            if (this.left <= 0) { return; }
            const timer = setInterval(() => {
                this.left--;
                if (this.left <= 0) { clearInterval(timer); }
            }, 1000);
        },
    }"
    x-bind:disabled="left > 0"
    {{ $attributes->merge(['class' => 'text-sm font-semibold text-[#019D91] transition hover:underline disabled:cursor-not-allowed disabled:text-neutral-400 disabled:no-underline']) }}
>
    <span x-show="left <= 0">Gửi lại mã</span>
    <span x-show="left > 0" x-cloak>Gửi lại mã sau <span x-text="left"></span> giây</span>
</button>
