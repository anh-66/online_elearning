@extends('layouts.instructor')

@section('title', 'Sửa khóa học')
@section('page_title', 'Sửa khóa học')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('instructor.courses.show', $course) }}" class="text-sm text-gray-500 hover:text-indigo-600">&larr; Quản lý nội dung</a>
            @include('instructor.partials.status-badge', ['status' => $course->status])
        </div>
    </div>

    @if($course->isRejected() && $course->reject_reason)
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-sm text-rose-800">
            <strong>Khóa học bị từ chối:</strong> {{ $course->reject_reason }}
        </div>
    @endif

    <form method="POST" action="{{ route('instructor.courses.update', $course) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        @include('instructor.courses.partials.form')

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('instructor.courses.show', $course) }}" class="px-5 py-2.5 rounded-xl bg-white border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50">Hủy</a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold shadow-sm hover:bg-indigo-700">Lưu thay đổi</button>
        </div>
    </form>

    <!-- Thư viện ảnh hiện có (form xóa nằm ngoài form chính để tránh lồng form) -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-base font-bold text-gray-900 mb-4">Thư viện ảnh hiện có ({{ $course->images->count() }})</h3>
        @if($course->images->isEmpty())
            <p class="text-sm text-gray-500">Chưa có ảnh nào trong thư viện.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($course->images as $image)
                    <div class="relative group">
                        <img src="{{ asset('storage/'.$image->image_path) }}" alt="Ảnh khóa học" class="aspect-video w-full object-cover rounded-lg border border-gray-200">
                        <form method="POST" action="{{ route('instructor.courses.images.destroy', [$course, $image]) }}" onsubmit="return confirm('Xóa ảnh này?')"
                              class="absolute top-1.5 right-1.5">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-7 h-7 rounded-full bg-white/90 text-rose-600 shadow flex items-center justify-center hover:bg-rose-600 hover:text-white" title="Xóa ảnh">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
