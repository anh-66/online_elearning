@extends('layouts.admin')

@section('title', 'Chi tiết phản hồi #' . $feedback->id)
@section('page_title', 'Chi Tiết Phản Hồi #' . $feedback->id)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.feedbacks.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-900 transition">
            <iconify-icon icon="lucide:arrow-left"></iconify-icon>
            <span>Quay lại danh sách</span>
        </a>

        <div class="flex items-center gap-2">
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $feedback->status_badge_class }}">
                {{ $feedback->status_label }}
            </span>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $feedback->priority_badge_class }}">
                Ưu tiên: {{ $feedback->priority_label }}
            </span>
        </div>
    </div>

    <div class="glass-card p-6 sm:p-8 space-y-6">
        <div>
            <div class="flex items-center gap-2 mb-2 text-xs font-semibold text-gray-400">
                <span>Loại: {{ $feedback->category_label }}</span>
                <span>&bull;</span>
                <span>Ngày gửi: {{ $feedback->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $feedback->title }}</h2>
        </div>

        <div class="p-4 bg-gray-50 rounded-xl flex items-center justify-between text-sm">
            <div>
                <span class="text-gray-500">Người gửi: </span>
                <strong class="text-gray-900">{{ $feedback->sender_name ?? $feedback->user?->name ?? 'Khách' }}</strong>
                @if($feedback->sender_email || $feedback->user?->email)
                    <span class="text-gray-400"> ({{ $feedback->sender_email ?? $feedback->user?->email }})</span>
                @endif
            </div>

            @if($feedback->user)
                <span class="px-2 py-0.5 rounded text-xs font-bold uppercase bg-emerald-100 text-emerald-700">Thành viên hệ thống</span>
            @else
                <span class="px-2 py-0.5 rounded text-xs font-bold uppercase bg-gray-200 text-gray-600">Khách</span>
            @endif
        </div>

        <div class="space-y-2">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nội dung chi tiết</h4>
            <div class="p-5 bg-gray-50 border border-gray-100 rounded-xl text-sm leading-relaxed text-gray-800 whitespace-pre-line">
                {{ $feedback->description }}
            </div>
        </div>

        @if($feedback->attachment)
            <div class="p-4 bg-blue-50/60 border border-blue-100 rounded-xl flex items-center justify-between">
                <div class="flex items-center gap-2 text-blue-900 text-sm">
                    <iconify-icon icon="lucide:paperclip" class="text-xl text-blue-600"></iconify-icon>
                    <span>Có tệp đính kèm đi cùng báo cáo</span>
                </div>
                <a href="{{ $feedback->attachment_url }}" target="_blank" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition inline-flex items-center gap-1.5">
                    <iconify-icon icon="lucide:download"></iconify-icon>
                    Tải về / Xem tệp
                </a>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.feedbacks.update', $feedback) }}" class="space-y-4 pt-4 border-t border-gray-100">
            @csrf
            @method('PATCH')

            <h4 class="text-sm font-bold text-gray-900">Xử lý phản hồi</h4>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Trạng thái xử lý</label>
                    <select name="status" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91]">
                        <option value="pending" {{ $feedback->status === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="in_progress" {{ $feedback->status === 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="resolved" {{ $feedback->status === 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
                        <option value="closed" {{ $feedback->status === 'closed' ? 'selected' : '' }}>Đã đóng</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Ghi chú nội bộ</label>
                <textarea name="admin_notes" rows="3" placeholder="Ghi chú thêm về việc xử lý lỗi hoặc giải đáp cho người học..."
                          class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] resize-none">{{ old('admin_notes', $feedback->admin_notes) }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-2">
                <button type="submit" class="px-6 py-2.5 bg-[#019D91] text-white text-sm font-semibold rounded-lg hover:bg-[#008b80] transition shadow-sm">
                    Lưu cập nhật
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
