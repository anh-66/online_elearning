@extends('layouts.instructor')

@section('title', 'Sửa quiz')
@section('page_title', 'Sửa quiz trắc nghiệm')

@section('content')
<form method="POST" action="{{ route('instructor.quizzes.update', $quiz) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <p class="text-sm text-gray-500">
        <a href="{{ route('instructor.quizzes.show', $quiz) }}" class="hover:text-indigo-600">&larr; {{ $quiz->title }}</a>
    </p>

    @include('instructor.quizzes.partials.form')

    <div class="flex items-center gap-3 max-w-3xl justify-end">
        <a href="{{ route('instructor.quizzes.show', $quiz) }}" class="px-5 py-2.5 rounded-xl bg-white border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50">Hủy</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-semibold shadow-sm hover:bg-violet-700">Lưu quiz</button>
    </div>
</form>
@endsection
