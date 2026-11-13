@extends('layouts.admin')

@section('title', 'Ảnh Cloudinary')
@section('page_title', 'Ảnh lưu trên Cloudinary')

@section('content')
<div x-data="cloudinaryGallery(@js(['targets' => $targets ?? [], 'folderDefaultTypes' => $folderDefaultTypes ?? [], 'trashDays' => $trashDays ?? 7]))" @keydown.escape.window="close()">
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Thư viện ảnh Cloudinary</h2>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-0.5">
                Ảnh mà website đã upload lên Cloudinary
                @if(!empty($rootFolder))
                    trong thư mục <code class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-200 text-xs">{{ $rootFolder }}</code>
                @endif
            </p>
        </div>
        @if($configured)
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.cloudinary.index', array_filter(['q' => $keyword ?? null, 'folder' => $folder ?? null, 'status' => $status ?? null, 'refresh' => 1], 'filled')) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-800 text-gray-700 dark:text-neutral-200 text-sm font-semibold rounded-xl hover:border-[#019D91] hover:text-[#019D91] dark:hover:text-[#02b3a5] transition-colors shadow-sm">
                    <iconify-icon icon="lucide:refresh-cw" class="text-lg"></iconify-icon>
                    Làm mới
                </a>
                <a href="https://console.cloudinary.com/console/media_library" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#019D91] text-white text-sm font-semibold rounded-xl hover:bg-[#008b80] transition-colors shadow-sm">
                    <iconify-icon icon="lucide:external-link" class="text-lg"></iconify-icon>
                    Mở Cloudinary
                </a>
            </div>
        @endif
    </div>

    @if($configured)
        @include('admin.cloudinary.partials.tabs', ['trashCount' => $trashCount ?? 0])
    @endif

    @if(!$configured)
        {{-- Chưa cấu hình Cloudinary --}}
        <div class="glass-card p-8 sm:p-10 text-center">
            <div class="mx-auto w-14 h-14 rounded-2xl bg-gray-100 dark:bg-neutral-800 text-gray-400 dark:text-neutral-500 flex items-center justify-center mb-4">
                <iconify-icon icon="lucide:cloud-off" class="text-3xl"></iconify-icon>
            </div>
            <h3 class="text-base font-bold text-gray-900 dark:text-neutral-100">Website chưa kết nối Cloudinary</h3>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-2 max-w-xl mx-auto">
                Biến môi trường <code class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-200 text-xs">CLOUDINARY_URL</code> đang trống,
                nên ảnh upload hiện được lưu trong <code class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-200 text-xs">storage/app/public</code> trên máy chủ.
            </p>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-2 max-w-xl mx-auto">
                Thêm <code class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-200 text-xs">CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME</code>
                vào file <code class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-200 text-xs">.env</code> rồi chạy <code class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-200 text-xs">php artisan config:clear</code> và tải lại trang này.
            </p>
        </div>
    @elseif(isset($error))
        {{-- Gọi Cloudinary thất bại --}}
        <div class="glass-card p-8 sm:p-10 text-center border-red-200 dark:border-red-500/30">
            <div class="mx-auto w-14 h-14 rounded-2xl bg-red-50 dark:bg-red-500/10 text-red-500 dark:text-red-400 flex items-center justify-center mb-4">
                <iconify-icon icon="lucide:alert-triangle" class="text-3xl"></iconify-icon>
            </div>
            <h3 class="text-base font-bold text-gray-900 dark:text-neutral-100">Không lấy được danh sách ảnh từ Cloudinary</h3>
            <p class="text-sm text-red-600 dark:text-red-400 mt-2 max-w-xl mx-auto break-words">{{ $error }}</p>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-2 max-w-xl mx-auto">
                Kiểm tra lại API key và API secret trong <code class="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-200 text-xs">CLOUDINARY_URL</code>, sau đó bấm <strong>Làm mới</strong>.
            </p>
        </div>
    @else
        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['icon' => 'lucide:images', 'label' => 'Tổng số ảnh', 'value' => number_format($stats['total'], 0, ',', '.')],
                ['icon' => 'lucide:hard-drive', 'label' => 'Dung lượng ảnh', 'value' => $stats['size']],
                ['icon' => 'lucide:unlink', 'label' => 'Ảnh chưa dùng', 'value' => number_format($unusedCount, 0, ',', '.')],
                ['icon' => 'lucide:clock', 'label' => 'Upload gần nhất', 'value' => $stats['latest'] ?? '—'],
            ] as $card)
                <div class="glass-card p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#e6f6f5] dark:bg-[#019D91]/15 text-[#019D91] dark:text-[#02b3a5] flex items-center justify-center shrink-0">
                        <iconify-icon icon="{{ $card['icon'] }}" class="text-xl"></iconify-icon>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-500 dark:text-neutral-400">{{ $card['label'] }}</p>
                        <p class="text-base font-bold text-gray-900 dark:text-neutral-100 truncate">{{ $card['value'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        @if($usage)
            @php
                $usageColor = $usage['credits_percent'] >= 90 ? 'bg-red-500' : ($usage['credits_percent'] >= 70 ? 'bg-amber-500' : 'bg-[#019D91]');
            @endphp
            <div class="glass-card p-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 text-sm">
                    <p class="font-semibold text-gray-900 dark:text-neutral-100">
                        <iconify-icon icon="lucide:gauge" class="text-[#019D91] dark:text-[#02b3a5] align-[-3px] mr-1"></iconify-icon>
                        Gói {{ $usage['plan'] ?? 'Cloudinary' }}: đã dùng {{ $usage['credits_used'] }} / {{ $usage['credits_limit'] }} credits ({{ $usage['credits_percent'] }}%)
                    </p>
                    <p class="text-xs text-gray-500 dark:text-neutral-400">Cả tài khoản: lưu trữ {{ $usage['storage'] }} · băng thông {{ $usage['bandwidth'] }}</p>
                </div>
                <div class="mt-3 h-2 rounded-full bg-gray-100 dark:bg-neutral-800 overflow-hidden">
                    <div class="h-full rounded-full {{ $usageColor }}" style="width: {{ max(1, $usage['credits_percent']) }}%"></div>
                </div>
            </div>
        @endif

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.cloudinary.index') }}" class="glass-card p-4 flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <iconify-icon icon="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500"></iconify-icon>
                <input type="text" name="q" value="{{ $keyword }}" placeholder="Tìm theo tên hoặc đường dẫn file..."
                       class="w-full pl-9 pr-3 py-2.5 rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-[#019D91]">
            </div>
            <select name="folder" class="md:w-72 py-2.5 rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-[#019D91]">
                <option value="">Tất cả thư mục ({{ $stats['total'] }})</option>
                @foreach($folders as $option)
                    <option value="{{ $option['value'] }}" @selected($folder === $option['value'])>
                        {{ $option['label'] }}{{ $option['label'] !== $option['value'] ? ' ('.$option['value'].')' : '' }} · {{ $option['count'] }}
                    </option>
                @endforeach
            </select>
            <select name="status" class="md:w-52 py-2.5 rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-[#019D91]">
                <option value="">Mọi trạng thái</option>
                <option value="unused" @selected($status === 'unused')>Chưa dùng ở đâu · {{ $unusedCount }}</option>
                <option value="used" @selected($status === 'used')>Đang được dùng</option>
            </select>
            <div class="flex gap-2">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#019D91] text-white text-sm font-semibold rounded-xl hover:bg-[#008b80] transition-colors">
                    <iconify-icon icon="lucide:filter" class="text-base"></iconify-icon>
                    Lọc
                </button>
                @if($keyword !== '' || filled($folder) || filled($status))
                    <a href="{{ route('admin.cloudinary.index') }}"
                       class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-neutral-300 rounded-xl hover:bg-gray-100 dark:hover:bg-neutral-800 transition-colors">
                        Xóa lọc
                    </a>
                @endif
            </div>
        </form>

        @if($unusedCount > 0 && $status !== 'unused')
            <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 flex flex-col sm:flex-row sm:items-center gap-3">
                <iconify-icon icon="lucide:unlink" class="text-2xl text-amber-600 dark:text-amber-400 shrink-0"></iconify-icon>
                <p class="flex-1 text-sm text-amber-800 dark:text-amber-300">
                    Có <strong>{{ $unusedCount }} ảnh</strong> chưa được dùng ở đâu, thường là ảnh bị mất liên kết với banner, khóa học hoặc avatar.
                    Bấm vào ảnh để gắn lại, không cần upload lại.
                </p>
                <a href="{{ route('admin.cloudinary.index', ['status' => 'unused']) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 transition-colors whitespace-nowrap">
                    Xem ảnh chưa dùng
                </a>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 text-xs text-gray-500 dark:text-neutral-400">
            <p>
                @if($images->total() > 0)
                    Hiển thị {{ $images->firstItem() }}–{{ $images->lastItem() }} trong {{ $images->total() }} ảnh
                @endif
            </p>
            <p>Dữ liệu lấy lúc {{ $fetchedAt->format('H:i d/m/Y') }} · tự cập nhật sau 5 phút</p>
        </div>

        @if($truncated)
            <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 text-amber-800 dark:text-amber-300 text-xs">
                Thư mục có quá nhiều ảnh nên trang này chỉ tải {{ number_format($stats['total'], 0, ',', '.') }} ảnh đầu tiên (xếp theo tên file). Xem đầy đủ trên Cloudinary Console.
            </div>
        @endif

        {{-- Gallery --}}
        @if($images->isEmpty())
            <div class="glass-card px-6 py-16 text-center">
                <div class="flex flex-col items-center gap-2">
                    <iconify-icon icon="lucide:image-off" class="text-gray-300 dark:text-neutral-600 text-4xl"></iconify-icon>
                    @if($stats['total'] === 0)
                        <p class="text-sm text-gray-500 dark:text-neutral-400">Chưa có ảnh nào trên Cloudinary.</p>
                        <p class="text-xs text-gray-400 dark:text-neutral-500">Ảnh sẽ hiện ở đây khi có người upload avatar, banner hoặc ảnh khóa học.</p>
                    @else
                        <p class="text-sm text-gray-500 dark:text-neutral-400">Không tìm thấy ảnh phù hợp với bộ lọc.</p>
                    @endif
                </div>
            </div>
        @else
            <div x-data="cloudinarySelection(@js($selection))" class="space-y-4">
                @component('admin.cloudinary.partials.selection-bar', [
                    'hint' => 'Tích vào ô ở góc mỗi ảnh để chọn nhiều ảnh rồi xóa cùng lúc.',
                    'total' => count($selection['allKeys']),
                    'hasPages' => $images->hasPages(),
                ])
                    <form method="POST" action="{{ route('admin.cloudinary.trash.bulk-store') }}"
                          @submit="if (! confirm(bulkTrashMessage(selected.length, usedCount()))) $event.preventDefault()">
                        @csrf
                        <input type="hidden" name="paths" :value="JSON.stringify(selected)">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition-colors shadow-sm whitespace-nowrap">
                            <iconify-icon icon="lucide:trash-2" class="text-base"></iconify-icon>
                            <span x-text="'Xóa ' + selected.length + ' ảnh'">Xóa ảnh đã chọn</span>
                        </button>
                    </form>
                @endcomponent

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                    @foreach($images as $image)
                        <div class="glass-card overflow-hidden group flex flex-col"
                             :class="isSelected(@js($image['path'])) && 'outline outline-2 outline-offset-0 outline-[#019D91] dark:outline-[#02b3a5]'">
                            <div class="relative">
                                {{-- Đang chọn nhiều thì bấm vào ảnh để chọn/bỏ chọn, không mở ảnh lớn --}}
                                <button type="button" @click="selected.length ? toggle(@js($image['path'])) : open(@js($image))"
                                        :title="selected.length ? 'Chọn / bỏ chọn ảnh này' : 'Xem ảnh lớn'"
                                        class="relative block w-full aspect-[4/3] bg-gray-100 dark:bg-neutral-800 overflow-hidden">
                                    <img src="{{ $image['thumb_url'] }}" alt="{{ $image['name'] }}" loading="lazy"
                                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                    @if($image['format'] !== '')
                                        <span class="absolute top-2 left-2 px-1.5 py-0.5 rounded-md bg-white/90 text-[10px] font-bold text-gray-700 shadow-sm">{{ $image['format'] }}</span>
                                    @endif
                                </button>
                                <label class="absolute top-1.5 right-1.5 inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/90 dark:bg-neutral-900/90 shadow-sm cursor-pointer"
                                       title="Chọn ảnh này">
                                    <input type="checkbox" :checked="isSelected(@js($image['path']))" @change="toggle(@js($image['path']))"
                                           aria-label="Chọn ảnh {{ $image['name'] }}"
                                           class="w-4 h-4 rounded border-gray-300 dark:border-neutral-600 text-[#019D91] dark:text-[#02b3a5] focus:ring-[#019D91] cursor-pointer">
                                </label>
                            </div>
                            <div class="p-3 flex-1 flex flex-col gap-1">
                                <p class="text-sm font-semibold text-gray-900 dark:text-neutral-100 truncate" title="{{ $image['path'] }}">{{ $image['name'] }}</p>
                                <span class="inline-flex self-start items-center gap-1 max-w-full px-2 py-0.5 rounded-full bg-[#e6f6f5] dark:bg-[#019D91]/15 text-[#019D91] dark:text-[#02b3a5] text-[11px] font-medium" title="{{ $image['folder'] === '' ? '/' : $image['folder'] }}">
                                    <iconify-icon icon="lucide:folder" class="shrink-0"></iconify-icon>
                                    <span class="truncate">{{ $image['folder_label'] }}</span>
                                </span>
                                @if($image['used_by'] !== [])
                                    <span class="inline-flex self-start items-center gap-1 max-w-full px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[11px] font-medium" title="{{ implode(' · ', $image['used_by']) }}">
                                        <iconify-icon icon="lucide:link" class="shrink-0"></iconify-icon>
                                        <span class="truncate">{{ $image['used_by'][0] }}</span>
                                        @if(count($image['used_by']) > 1)
                                            <span class="shrink-0">+{{ count($image['used_by']) - 1 }}</span>
                                        @endif
                                    </span>
                                @elseif($image['tracked'])
                                    <span class="inline-flex self-start items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-[11px] font-medium">
                                        <iconify-icon icon="lucide:unlink" class="shrink-0"></iconify-icon>
                                        Chưa dùng
                                    </span>
                                @endif
                                <p class="text-xs text-gray-500 dark:text-neutral-400">
                                    @if($image['width'] && $image['height'])
                                        {{ $image['width'] }}×{{ $image['height'] }} ·
                                    @endif
                                    {{ $image['size'] }}
                                </p>
                                <p class="text-xs text-gray-400 dark:text-neutral-500">{{ $image['uploaded_at'] }}</p>
                                <div class="flex items-center gap-1 pt-1 mt-auto">
                                    <button type="button" @click="open(@js($image))" title="Gắn ảnh này vào banner, khóa học hoặc avatar"
                                            class="inline-flex items-center gap-1 px-2 py-1 mr-auto whitespace-nowrap text-xs font-semibold text-[#019D91] dark:text-[#02b3a5] hover:bg-[#e6f6f5] dark:hover:bg-[#019D91]/20 rounded-lg transition-all">
                                        <iconify-icon icon="lucide:link-2" class="text-sm shrink-0"></iconify-icon>
                                        Gắn
                                    </button>
                                    <button type="button" @click="copy(@js($image['url']))" title="Sao chép link ảnh"
                                            class="p-1.5 text-gray-400 dark:text-neutral-500 hover:text-[#019D91] dark:hover:text-[#02b3a5] hover:bg-[#e6f6f5] dark:hover:bg-[#019D91]/20 rounded-lg transition-all">
                                        <iconify-icon x-show="copiedUrl !== @js($image['url'])" icon="lucide:copy" class="text-base"></iconify-icon>
                                        <iconify-icon x-show="copiedUrl === @js($image['url'])" x-cloak icon="lucide:check" class="text-base text-emerald-600 dark:text-emerald-400"></iconify-icon>
                                    </button>
                                    <a href="{{ $image['url'] }}" target="_blank" rel="noopener noreferrer" title="Mở ảnh gốc"
                                       class="p-1.5 text-gray-400 dark:text-neutral-500 hover:text-[#019D91] dark:hover:text-[#02b3a5] hover:bg-[#e6f6f5] dark:hover:bg-[#019D91]/20 rounded-lg transition-all">
                                        <iconify-icon icon="lucide:external-link" class="text-base"></iconify-icon>
                                    </a>
                                    @php
                                        $trashConfirm = (count($image['places']) > 0
                                            ? 'Ảnh đang dùng ở '.count($image['places']).' chỗ. Xóa sẽ tháo ảnh khỏi các chỗ đó và chuyển vào thùng rác.'
                                            : 'Chuyển ảnh này vào thùng rác?').' Sau '.$trashDays.' ngày ảnh sẽ bị xóa vĩnh viễn trên Cloudinary.';
                                    @endphp
                                    <form method="POST" action="{{ route('admin.cloudinary.trash.store') }}" onsubmit="return confirm(@js($trashConfirm))">
                                        @csrf
                                        <input type="hidden" name="path" value="{{ $image['path'] }}">
                                        <button type="submit" title="Xóa ảnh (chuyển vào thùng rác)"
                                                class="p-1.5 text-gray-400 dark:text-neutral-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-all">
                                            <iconify-icon icon="lucide:trash-2" class="text-base"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($images->hasPages())
                    <div class="glass-card px-6 py-4">
                        {{ $images->links() }}
                    </div>
                @endif
            </div>
        @endif
    @endif

</div>

    {{-- Xem ảnh lớn (đặt ngoài space-y-6 để lớp phủ không bị đẩy xuống) --}}
    <template x-if="preview">
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-gray-900/70" @click.self="close()">
            <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col md:flex-row">
                <div class="bg-gray-900 flex items-center justify-center md:w-2/3 min-h-[240px]">
                    <img :src="preview.preview_url" :alt="preview.name" class="max-h-[50vh] md:max-h-[90vh] w-auto object-contain">
                </div>
                <div class="md:w-1/3 p-5 overflow-y-auto space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-neutral-100 break-all" x-text="preview.name"></h3>
                        <button type="button" @click="close()" class="text-gray-400 dark:text-neutral-500 hover:text-gray-700 dark:hover:text-neutral-200 shrink-0" aria-label="Đóng">
                            <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
                        </button>
                    </div>
                    <dl class="space-y-2.5 text-xs">
                        <div>
                            <dt class="text-gray-500 dark:text-neutral-400">Thư mục</dt>
                            <dd class="font-medium text-gray-900 dark:text-neutral-100" x-text="preview.folder_label + (preview.folder ? ' (' + preview.folder + ')' : '')"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-neutral-400">Đường dẫn lưu trong database</dt>
                            <dd class="font-mono text-gray-900 dark:text-neutral-100 break-all" x-text="preview.path"></dd>
                        </div>
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <dt class="text-gray-500 dark:text-neutral-400">Kích thước</dt>
                                <dd class="font-medium text-gray-900 dark:text-neutral-100" x-text="preview.width && preview.height ? preview.width + '×' + preview.height + ' px' : '—'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-neutral-400">Dung lượng</dt>
                                <dd class="font-medium text-gray-900 dark:text-neutral-100" x-text="preview.size"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-neutral-400">Định dạng</dt>
                                <dd class="font-medium text-gray-900 dark:text-neutral-100" x-text="preview.format || '—'"></dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-neutral-400">Ngày upload</dt>
                                <dd class="font-medium text-gray-900 dark:text-neutral-100" x-text="preview.uploaded_at || '—'"></dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-neutral-400 mb-1">Link ảnh</dt>
                            <dd class="flex gap-1.5">
                                <input type="text" readonly :value="preview.url" @focus="$event.target.select()"
                                       class="flex-1 min-w-0 px-2 py-1.5 rounded-lg border-gray-200 dark:border-neutral-800 bg-gray-50 dark:bg-neutral-800/40 text-[11px] font-mono">
                                <button type="button" @click="copy(preview.url)"
                                        class="px-2.5 rounded-lg bg-[#e6f6f5] dark:bg-[#019D91]/15 text-[#019D91] dark:text-[#02b3a5] text-[11px] font-semibold hover:bg-[#d3efed] dark:hover:bg-[#019D91]/25 whitespace-nowrap"
                                        x-text="copiedUrl === preview.url ? 'Đã chép' : 'Sao chép'"></button>
                            </dd>
                        </div>
                    </dl>

                    <div class="rounded-xl p-3 text-xs"
                         :class="preview.used_by.length ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300' : 'bg-amber-50 dark:bg-amber-500/10 text-amber-800 dark:text-amber-300'">
                        <template x-if="preview.used_by.length">
                            <div>
                                <p class="font-semibold mb-1.5">Đang được dùng cho</p>
                                <ul class="space-y-1.5">
                                    <template x-for="place in preview.places" :key="place.type + place.id">
                                        <li class="flex items-center justify-between gap-2">
                                            <span class="min-w-0 break-words" x-text="place.label"></span>
                                            <form method="POST" action="{{ route('admin.cloudinary.unlink') }}"
                                                  @submit="if (! confirm('Tháo ảnh khỏi ' + place.label + '? Chỗ đó sẽ thành trống ảnh, ảnh vẫn còn trên Cloudinary.')) $event.preventDefault()">
                                                @csrf
                                                <input type="hidden" name="path" :value="preview.path">
                                                <input type="hidden" name="type" :value="place.type">
                                                <input type="hidden" name="id" :value="place.id">
                                                <button type="submit" title="Tháo ảnh khỏi chỗ này, vẫn giữ ảnh trên Cloudinary"
                                                        class="inline-flex items-center gap-1 shrink-0 px-2 py-1 rounded-lg bg-white dark:bg-neutral-900 border border-emerald-200 dark:border-emerald-500/30 text-[11px] font-semibold text-emerald-700 dark:text-emerald-300 hover:border-emerald-400 transition-colors">
                                                    <iconify-icon icon="lucide:unlink" class="text-xs"></iconify-icon>
                                                    Tháo
                                                </button>
                                            </form>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </template>
                        <template x-if="!preview.used_by.length">
                            <p x-text="preview.tracked ? 'Ảnh này chưa được dùng ở đâu trên website.' : 'Ảnh trong bài viết: website không theo dõi ảnh này đang dùng ở đâu.'"></p>
                        </template>
                    </div>

                    {{-- Gắn ảnh có sẵn vào banner / ảnh bìa khóa học / avatar --}}
                    <form method="POST" action="{{ route('admin.cloudinary.assign') }}" class="space-y-2.5 pt-4 border-t border-gray-100 dark:border-neutral-800">
                        @csrf
                        <input type="hidden" name="path" :value="preview.path">
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-neutral-100">Gắn ảnh này vào</p>
                            <p class="text-[11px] text-gray-500 dark:text-neutral-400 mt-0.5">Chỉ hiện những mục chưa có ảnh hoặc ảnh đang lỗi.</p>
                        </div>
                        <select name="type" x-model="assignType" @change="assignId = ''"
                                class="w-full py-2 rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-[#019D91]">
                            <template x-for="(group, key) in targets" :key="key">
                                <option :value="key" x-text="group.label" :selected="key === assignType"></option>
                            </template>
                        </select>
                        <select name="target_id" x-model="assignId" required
                                class="w-full py-2 rounded-xl border-gray-200 dark:border-neutral-800 text-sm focus:border-[#019D91] focus:ring-[#019D91]">
                            <option value="" x-text="missingTargets().length ? 'Chọn nơi gắn ảnh...' : 'Không còn mục nào thiếu ảnh'">Chọn nơi gắn ảnh...</option>
                            <template x-for="item in missingTargets()" :key="assignType + item.id">
                                <option :value="String(item.id)" x-text="optionLabel(item)" :selected="String(item.id) === assignId"></option>
                            </template>
                        </select>
                        <button type="submit" :disabled="!assignId"
                                class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-[#019D91] text-white text-sm font-semibold rounded-xl hover:bg-[#008b80] transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <iconify-icon icon="lucide:link-2" class="text-base"></iconify-icon>
                            Gắn ảnh
                        </button>
                        <p class="text-[11px] text-gray-500 dark:text-neutral-400">Chỉ đổi ảnh trong database. Ảnh cũ của mục đó (nếu có) vẫn còn trên Cloudinary.</p>
                    </form>

                    <a :href="preview.url" target="_blank" rel="noopener noreferrer"
                       class="flex items-center justify-center gap-2 w-full px-4 py-2.5 border border-gray-200 dark:border-neutral-800 text-gray-700 dark:text-neutral-200 text-sm font-semibold rounded-xl hover:border-[#019D91] hover:text-[#019D91] dark:hover:text-[#02b3a5] transition-colors">
                        <iconify-icon icon="lucide:external-link" class="text-base"></iconify-icon>
                        Mở ảnh gốc
                    </a>

                    <form method="POST" action="{{ route('admin.cloudinary.trash.store') }}" @submit="if (! confirm(trashMessage(preview))) $event.preventDefault()">
                        @csrf
                        <input type="hidden" name="path" :value="preview.path">
                        <button type="submit"
                                class="flex items-center justify-center gap-2 w-full px-4 py-2.5 border border-red-200 dark:border-red-500/30 text-red-600 dark:text-red-400 text-sm font-semibold rounded-xl hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                            <iconify-icon icon="lucide:trash-2" class="text-base"></iconify-icon>
                            Xóa ảnh
                        </button>
                        <p class="text-[11px] text-gray-500 dark:text-neutral-400 mt-1.5 text-center" x-text="'Ảnh vào thùng rác, tự xóa vĩnh viễn trên Cloudinary sau ' + trashDays + ' ngày.'"></p>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
    function cloudinaryGallery(config) {
        return {
            preview: null,
            copiedUrl: null,
            targets: config.targets || {},
            folderDefaultTypes: config.folderDefaultTypes || {},
            trashDays: config.trashDays || 7,
            assignType: 'course',
            assignId: '',
            open(image) {
                this.assignType = this.folderDefaultTypes[image.folder] || 'course';
                this.assignId = '';
                this.preview = image;
            },
            // Ẩn những mục đã có ảnh chạy được, chỉ để lại mục đang trống hoặc ảnh lỗi.
            missingTargets() {
                return (this.targets[this.assignType]?.items ?? []).filter((item) => item.status !== 'ok');
            },
            bulkTrashMessage(count, used) {
                return 'Chuyển ' + count + ' ảnh đã chọn vào thùng rác?'
                    + (used ? ' Có ' + used + ' ảnh đang được dùng, sẽ bị tháo khỏi những chỗ đó.' : '')
                    + ' Sau ' + this.trashDays + ' ngày ảnh sẽ bị xóa vĩnh viễn trên Cloudinary.';
            },
            trashMessage(image) {
                const count = (image.places || []).length;
                const intro = count
                    ? 'Ảnh đang dùng ở ' + count + ' chỗ. Xóa sẽ tháo ảnh khỏi các chỗ đó và chuyển vào thùng rác.'
                    : 'Chuyển ảnh này vào thùng rác?';

                return intro + ' Sau ' + this.trashDays + ' ngày ảnh sẽ bị xóa vĩnh viễn trên Cloudinary.';
            },
            optionLabel(item) {
                const notes = { empty: ' (đang trống ảnh)', broken: ' (ảnh đang lỗi)' };

                return item.label + (notes[item.status] || '');
            },
            close() {
                this.preview = null;
            },
            async copy(url) {
                try {
                    await navigator.clipboard.writeText(url);
                } catch (error) {
                    // Trình duyệt chặn clipboard (vd: không phải HTTPS): dùng cách cũ.
                    const input = document.createElement('textarea');
                    input.value = url;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    input.remove();
                }

                this.copiedUrl = url;
                setTimeout(() => {
                    if (this.copiedUrl === url) {
                        this.copiedUrl = null;
                    }
                }, 1800);
            },
        };
    }
</script>
@endpush
