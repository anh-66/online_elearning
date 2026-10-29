@extends('layouts.instructor')

@section('title', 'Tạo khóa học mới')
@section('page_title', 'Tạo khóa học mới')

@section('content')
<form method="POST" action="{{ route('instructor.courses.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <div class="p-4 rounded-xl bg-indigo-50 border border-indigo-100 text-sm text-indigo-800">
        Khóa học mới sẽ được lưu ở trạng thái <strong>Bản nháp</strong>. Sau khi thêm chương và bài học, bạn có thể gửi cho quản trị viên duyệt.
    </div>

    @include('instructor.courses.partials.form')

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('instructor.courses.index') }}" class="px-5 py-2.5 rounded-xl bg-white border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50">Hủy</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold shadow-sm hover:bg-indigo-700">Tạo khóa học</button>
    </div>
</form>
@endsection
