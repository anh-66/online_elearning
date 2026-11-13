{{-- Tab chuyển giữa Thư viện ảnh và Thùng rác --}}
@php
    $tabs = [
        ['route' => 'admin.cloudinary.index', 'icon' => 'lucide:images', 'label' => 'Thư viện ảnh', 'count' => null],
        ['route' => 'admin.cloudinary.trash.index', 'icon' => 'lucide:trash-2', 'label' => 'Thùng rác', 'count' => $trashCount ?? 0],
    ];
@endphp
<nav class="inline-flex gap-1 p-1 rounded-xl bg-gray-100 dark:bg-neutral-900 border border-gray-200 dark:border-neutral-800" aria-label="Ảnh Cloudinary">
    @foreach($tabs as $tab)
        @php $isActive = request()->routeIs($tab['route']); @endphp
        <a href="{{ route($tab['route']) }}" @if($isActive) aria-current="page" @endif
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ $isActive ? 'bg-white dark:bg-neutral-800 text-[#019D91] dark:text-[#02b3a5] shadow-sm' : 'text-gray-600 dark:text-neutral-400 hover:text-gray-900 dark:hover:text-neutral-100' }}">
            <iconify-icon icon="{{ $tab['icon'] }}" class="text-base"></iconify-icon>
            {{ $tab['label'] }}
            @if($tab['count'])
                <span class="min-w-[1.25rem] px-1.5 py-0.5 rounded-full text-[11px] leading-none text-center {{ $isActive ? 'bg-[#e6f6f5] dark:bg-[#019D91]/20' : 'bg-red-100 dark:bg-red-500/15 text-red-600 dark:text-red-400' }}">{{ $tab['count'] }}</span>
            @endif
        </a>
    @endforeach
</nav>
