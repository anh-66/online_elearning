@extends('layouts.instructor')

@section('title', 'Thêm bài học')
@section('page_title', 'Thêm bài học mới')

@section('content')
<form method="POST" action="{{ route('instructor.chapters.lessons.store', $chapter) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <p class="text-sm text-gray-500">
        <a href="{{ route('instructor.courses.show', $course) }}" class="hover:text-indigo-600">&larr; {{ $course->title }}</a>
    </p>

    @include('instructor.lessons.partials.form')

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('instructor.courses.show', $course) }}" class="px-5 py-2.5 rounded-xl bg-white border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50">Hủy</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold shadow-sm hover:bg-indigo-700">Thêm bài học</button>
    </div>
</form>
@endsection
