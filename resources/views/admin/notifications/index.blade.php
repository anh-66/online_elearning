@extends('layouts.admin')

@section('title', 'Quản lý banner')
@section('page_title', 'Quản lý Banner trang chủ')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">Chỉ banner đang bật mới hiển thị trên trang chủ, sắp xếp theo thứ tự tăng dần.</p>
        <a href="{{ route('admin.banners.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700 whitespace-nowrap">+ Thêm banner</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">Banner</th>
                        <th class="px-4 py-3 text-left font-semibold">Liên kết</th>
                        <th class="px-4 py-3 text-center font-semibold">Thứ tự</th>
                        <th class="px-4 py-3 text-left font-semibold">Trạng thái</th>
                        <th class="px-6 py-3 text-right font-semibold">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($banners as $banner)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-28 h-14 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                        @if($banner->image_path)
                                            <img src="{{ asset('storage/'.$banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <p class="font-semibold text-gray-900">{{ $banner->title }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-gray-500 max-w-xs truncate">{{ $banner->link_url ?: '—' }}</td>
                            <td class="px-4 py-4 text-center text-gray-600">{{ $banner->order }}</td>
                            <td class="px-4 py-4">
                                @if($banner->is_active)
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Đang bật</span>
                                @else
                                    <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">Đã tắt</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.banners.edit', $banner) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Sửa</a>
                                    <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" onsubmit="return confirm('Xóa banner này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">Chưa có banner nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $banners->links() }}
</div>
@endsection
