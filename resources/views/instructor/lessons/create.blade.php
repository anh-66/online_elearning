@extends('layouts.instructor')

@section('title', 'Thêm bài học')
@section('page_title', 'Thêm bài học mới')

@section('content')
<form method="POST" action="{{ route('instructor.chapters.lessons.store', $chapter) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <p class="text-sm text-gray-500 dark:text-neutral-400">
        <a href="{{ route('instructor.courses.show', $course) }}" class="hover:text-brand-700 dark:hover:text-brand-300">&larr; {{ $course->title }}</a>
    </p>

    @include('instructor.lessons.partials.form')

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('instructor.courses.show', $course) }}" class="px-5 py-2.5 rounded-xl bg-white dark:bg-neutral-900 border border-gray-300 dark:border-neutral-700 text-sm font-semibold text-gray-700 dark:text-neutral-200 hover:bg-gray-50 dark:hover:bg-neutral-800">Hủy</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-700 text-white text-sm font-semibold shadow-sm hover:bg-brand-800">Thêm bài học</button>
    </div>
</form>
@endsection
