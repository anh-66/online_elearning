@props(['seconds' => 0, 'sendsLeft' => null])

@php
    $maxPerHour = (int) config('otp.max_sends_per_hour', 5);
    $sendsLeft = $sendsLeft === null ? null : max(0, (int) $sendsLeft);
@endphp

{{-- Nút "Gửi lại mã" có đếm ngược, dùng bên trong một form POST. Thời gian chờ tăng dần sau mỗi lần gửi. --}}
<span class="inline-flex flex-col items-end gap-0.5">
    <button
        type="submit"
        x-data="{
            until: Date.now() + {{ max(0, (int) $seconds) }} * 1000,
            left: {{ max(0, (int) $seconds) }},
            init() {
                if (this.left <= 0) { return; }
                const timer = setInterval(() => {
                    this.left = Math.max(0, Math.ceil((this.until - Date.now()) / 1000));
                    if (this.left <= 0) { clearInterval(timer); }
                }, 1000);
            },
            label() {
                if (this.left < 60) { return this.left + ' giây'; }
                const minutes = Math.floor(this.left / 60);
                const rest = String(this.left % 60).padStart(2, '0');
                return minutes + ':' + rest;
            },
        }"
        x-bind:disabled="left > 0"
        {{ $attributes->merge(['class' => 'text-sm font-semibold text-[#019D91] transition hover:underline disabled:cursor-not-allowed disabled:text-neutral-400 disabled:no-underline']) }}
    >
        <span x-show="left <= 0">Gửi lại mã</span>
        <span x-show="left > 0" x-cloak>Gửi lại mã sau <span x-text="label()"></span></span>
    </button>

    @if ($sendsLeft !== null && $sendsLeft < $maxPerHour)
        <span class="text-[11px] text-neutral-400">
            @if ($sendsLeft === 0)
                Đã hết lượt gửi trong giờ này
            @else
                Còn {{ $sendsLeft }} lần gửi trong giờ này
            @endif
        </span>
    @endif
</span>
