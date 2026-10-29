@extends('layouts.instructor')

@section('title', 'Tạo quiz')
@section('page_title', 'Tạo quiz trắc nghiệm')

@section('content')
<form method="POST" action="{{ route('instructor.courses.quizzes.store', $course) }}" class="space-y-6">
    @csrf

    <p class="text-sm text-gray-500">
        <a href="{{ route('instructor.courses.show', $course) }}" class="hover:text-indigo-600">&larr; {{ $course->title }}</a>
    </p>

    @include('instructor.quizzes.partials.form')

    <div class="flex items-center gap-3 max-w-3xl justify-end">
        <a href="{{ route('instructor.courses.show', $course) }}" class="px-5 py-2.5 rounded-xl bg-white border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50">Hủy</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-semibold shadow-sm hover:bg-violet-700">Tạo quiz</button>
    </div>
</form>
@endsection
