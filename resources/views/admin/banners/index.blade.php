@extends('layouts.admin')

@section('title', 'Quản lý banner')
@section('page_title', 'Quản lý Banner')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Danh sách Banner quảng bá</h2>
            <p class="text-sm text-gray-500 mt-0.5">Chỉ banner đang bật (Active) mới hiển thị trên slider trang chủ.</p>
        </div>
        <a href="{{ route('admin.banners.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#019D91] text-white text-sm font-semibold rounded-xl hover:bg-[#008b80] transition-colors shadow-sm">
            <iconify-icon icon="lucide:plus" class="text-lg"></iconify-icon>
            Thêm banner
        </a>
    </div>

    {{-- Table --}}
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 text-gray-500 text-xs font-semibold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Banner</th>
                        <th class="px-4 py-4">Liên kết URL</th>
                        <th class="px-4 py-4 text-center">Thứ tự</th>
                        <th class="px-4 py-4">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($banners as $banner)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            {{-- Banner Image & Title --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-28 h-14 rounded-xl bg-gray-100 overflow-hidden shrink-0 border border-gray-200 flex items-center justify-center">
                                        @if($banner->image_path)
                                            <img src="{{ asset('storage/'.$banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover"
                                                 onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                            <div class="hidden w-full h-full flex items-center justify-center text-gray-400">
                                                <iconify-icon icon="lucide:image" class="text-xl"></iconify-icon>
                                            </div>
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <iconify-icon icon="lucide:image" class="text-xl"></iconify-icon>
                                            </div>
                                        @endif
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $banner->title }}</p>
                                </div>
                            </td>

                            {{-- Link URL --}}
                            <td class="px-4 py-4 text-xs text-gray-500 max-w-xs truncate">
                                {{ $banner->link_url ?: '—' }}
                            </td>

                            {{-- Order --}}
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex w-8 h-8 items-center justify-center rounded-lg bg-gray-100 text-sm font-bold text-gray-700">
                                    {{ $banner->order }}
                                </span>
                            </td>

                            {{-- Toggle Status --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.banners.toggle', $banner) }}" class="inline" data-confirm-message="{{ $banner->is_active ? 'Tắt hiển thị banner này?' : 'Bật hiển thị banner này?' }}">
                                    @csrf
                                    @method('PATCH')
                                    @if($banner->is_active)
                                        <button type="submit"
                                                class="status-badge bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors cursor-pointer">
                                            ● Đang bật
                                        </button>
                                    @else
                                        <button type="submit"
                                                class="status-badge bg-gray-100 text-gray-500 hover:bg-gray-200 transition-colors cursor-pointer">
                                            ○ Đã tắt
                                        </button>
                                    @endif
                                </form>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.banners.edit', $banner) }}"
                                       title="Chỉnh sửa"
                                       class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all">
                                        <iconify-icon icon="lucide:edit-3" class="text-lg"></iconify-icon>
                                    </a>
                                    <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}"
                                          data-confirm-message="Bạn có chắc chắn muốn xóa banner này?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Xóa"
                                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                            <iconify-icon icon="lucide:trash-2" class="text-lg"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <iconify-icon icon="lucide:image" class="text-gray-300 text-4xl"></iconify-icon>
                                    <p class="text-sm text-gray-500">Chưa có banner nào được tạo</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($banners->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                {{ $banners->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
