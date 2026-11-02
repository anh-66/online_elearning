@extends('layouts.instructor')

@section('title', 'Tạo khóa học mới')
@section('page_title', 'Tạo khóa học mới')

@section('content')
<form method="POST" action="{{ route('instructor.courses.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <div class="p-4 rounded-xl bg-brand-50 dark:bg-brand-500/10 border border-brand-100 dark:border-brand-500/30 text-sm text-brand-900 dark:text-brand-300">
        Khóa học mới sẽ được lưu ở trạng thái <strong>Bản nháp</strong>. Sau khi thêm chương và bài học, bạn có thể gửi cho quản trị viên duyệt.
    </div>

    @include('instructor.courses.partials.form')

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('instructor.courses.index') }}" class="px-5 py-2.5 rounded-xl bg-white dark:bg-neutral-900 border border-gray-300 dark:border-neutral-700 text-sm font-semibold text-gray-700 dark:text-neutral-200 hover:bg-gray-50 dark:hover:bg-neutral-800">Hủy</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-700 text-white text-sm font-semibold shadow-sm hover:bg-brand-800">Tạo khóa học</button>
    </div>
</form>
@endsection
