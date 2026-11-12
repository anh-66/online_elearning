@extends('layouts.admin')

@section('title', 'Thùng rác ảnh Cloudinary')
@section('page_title', 'Ảnh lưu trên Cloudinary')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Thùng rác</h2>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-0.5">
                Ảnh đã xóa được giữ {{ $days }} ngày, sau đó tự xóa vĩnh viễn trên Cloudinary.
            </p>
        </div>
        @if($items->total() > 0)
            <form method="POST" action="{{ route('admin.cloudinary.trash.empty') }}"
                  onsubmit="return confirm(@js('Xóa vĩnh viễn tất cả '.$items->total().' ảnh trong thùng rác? Ảnh sẽ bị xóa luôn trên Cloudinary và không khôi phục được.'))">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-neutral-900 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 text-sm font-semibold rounded-xl hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors shadow-sm">
                    <iconify-icon icon="lucide:trash" class="text-lg"></iconify-icon>
                    Dọn sạch thùng rác
                </button>
            </form>
        @endif
    </div>

    @include('admin.cloudinary.partials.tabs', ['trashCount' => $items->total()])

    @if(!$configured)
        <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 text-sm text-amber-800 dark:text-amber-300">
            Website chưa kết nối Cloudinary (thiếu <code>CLOUDINARY_URL</code>) nên chưa xóa vĩnh viễn được ảnh nào.
        </div>
    @endif

    <div class="p-4 rounded-xl bg-sky-50 dark:bg-sky-500/10 border border-sky-200 dark:border-sky-500/30 text-sm text-sky-800 dark:text-sky-300 flex items-start gap-2">
        <iconify-icon icon="lucide:info" class="text-lg shrink-0 mt-0.5"></iconify-icon>
        <span>
            Ảnh trong thùng rác đã được tháo khỏi website nhưng vẫn còn trên Cloudinary.
            <strong>Khôi phục</strong> sẽ gắn ảnh lại vào chỗ cũ nếu chỗ đó vẫn đang trống.
            Ảnh quá {{ $days }} ngày được xóa vĩnh viễn khi admin mở trang này hoặc khi server khởi động lại.
            @if($purged['purged'] > 0)
                <span class="block mt-1 font-semibold">Vừa tự xóa vĩnh viễn {{ $purged['purged'] }} ảnh đã quá {{ $days }} ngày.</span>
            @endif
        </span>
    </div>

    @if($items->isEmpty())
        <div class="glass-card px-6 py-16 text-center">
            <div class="flex flex-col items-center gap-2">
                <iconify-icon icon="lucide:trash-2" class="text-gray-300 dark:text-neutral-600 text-4xl"></iconify-icon>
                <p class="text-sm text-gray-500 dark:text-neutral-400">Thùng rác đang trống.</p>
                <a href="{{ route('admin.cloudinary.index') }}" class="text-sm font-semibold text-[#019D91] dark:text-[#02b3a5] hover:underline">Về thư viện ảnh</a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($items as $item)
                @php $daysLeft = $item->daysLeft(); @endphp
                <div class="glass-card overflow-hidden flex flex-col">
                    <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer" title="Mở ảnh gốc"
                       class="relative block aspect-[4/3] bg-gray-100 dark:bg-neutral-800 overflow-hidden">
                        <img src="{{ $item->thumb_url }}" alt="{{ $item->name }}" loading="lazy" class="w-full h-full object-cover opacity-80">
                        <span class="absolute top-2 left-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold shadow-sm {{ $daysLeft <= 1 ? 'bg-red-600 text-white' : 'bg-white/90 text-gray-800' }}">
                            <iconify-icon icon="lucide:clock"></iconify-icon>
                            {{ $daysLeft === 0 ? 'Xóa trong lần dọn tới' : 'Còn '.$daysLeft.' ngày' }}
                        </span>
                    </a>
                    <div class="p-4 flex-1 flex flex-col gap-2">
                        <p class="text-sm font-semibold text-gray-900 dark:text-neutral-100 truncate" title="{{ $item->path }}">{{ $item->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-neutral-400">
                            Xóa lúc {{ $item->created_at->timezone(config('app.timezone'))->format('H:i d/m/Y') }}
                            @if($item->deleter) bởi {{ $item->deleter->name }} @endif
                        </p>
                        <p class="text-xs text-gray-500 dark:text-neutral-400">
                            Xóa vĩnh viễn: {{ $item->purge_at->timezone(config('app.timezone'))->format('d/m/Y') }}
                        </p>
                        @if(! empty($item->places))
                            <div class="text-xs text-gray-600 dark:text-neutral-300">
                                <p class="font-semibold">Từng dùng cho:</p>
                                <ul class="list-disc pl-4 mt-0.5 space-y-0.5">
                                    @foreach($item->places as $place)
                                        <li class="break-words">{{ $place['label'] ?? '' }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="flex flex-wrap gap-2 pt-2 mt-auto">
                            <form method="POST" action="{{ route('admin.cloudinary.trash.restore', $item) }}" class="flex-1">
                                @csrf
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 whitespace-nowrap rounded-xl bg-[#019D91] text-white text-xs font-semibold hover:bg-[#008b80] transition-colors">
                                    <iconify-icon icon="lucide:rotate-ccw" class="text-sm"></iconify-icon>
                                    Khôi phục
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.cloudinary.trash.destroy', $item) }}" class="flex-1"
                                  onsubmit="return confirm(@js('Xóa vĩnh viễn ảnh '.$item->name.'? Ảnh sẽ bị xóa luôn trên Cloudinary và không khôi phục được.'))">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 whitespace-nowrap rounded-xl border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 text-xs font-semibold hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                    <iconify-icon icon="lucide:trash-2" class="text-sm"></iconify-icon>
                                    Xóa vĩnh viễn
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($items->hasPages())
            <div class="glass-card px-6 py-4">
                {{ $items->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
