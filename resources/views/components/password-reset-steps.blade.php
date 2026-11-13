@props(['current' => 1])

@php
    $steps = ['Nhập email', 'Nhập mã', 'Mật khẩu mới'];
@endphp

<ol class="mb-6 flex items-center gap-2" aria-label="Các bước đặt lại mật khẩu">
    @foreach ($steps as $index => $label)
        @php
            $number = $index + 1;
            $done = $number < $current;
            $active = $number === $current;
        @endphp
        <li class="flex items-center gap-2 {{ $loop->last ? '' : 'flex-1' }}" @if ($active) aria-current="step" @endif>
            <span @class([
                'flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[11px] font-bold',
                'bg-[#019D91] text-white' => $done,
                'bg-[#019D91] text-white ring-4 ring-[#019D91]/15' => $active,
                'bg-neutral-100 text-neutral-400' => ! $done && ! $active,
            ])>
                @if ($done)
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-8 8a1 1 0 0 1-1.4 0l-4-4a1 1 0 1 1 1.4-1.4L8 12.58l7.3-7.3a1 1 0 0 1 1.4 0Z" clip-rule="evenodd" /></svg>
                    <span class="sr-only">Đã xong:</span>
                @else
                    {{ $number }}
                @endif
            </span>
            <span @class([
                'whitespace-nowrap text-xs font-semibold',
                'text-neutral-900' => $active,
                'hidden sm:inline text-[#018076]' => $done,
                'hidden sm:inline text-neutral-400' => ! $done && ! $active,
            ])>{{ $label }}</span>
            @unless ($loop->last)
                <span class="h-px min-w-3 flex-1 {{ $done ? 'bg-[#019D91]/50' : 'bg-neutral-200' }}" aria-hidden="true"></span>
            @endunless
        </li>
    @endforeach
</ol>
