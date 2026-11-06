@extends('layouts.instructor')

@section('title', 'Sửa quiz')
@section('page_title', 'Sửa quiz trắc nghiệm')

@section('content')
<form method="POST" action="{{ route('instructor.quizzes.update', $quiz) }}" class="space-y-6">
    @csrf
    @method('PUT')

    <p class="text-sm text-gray-500 dark:text-neutral-400">
        <a href="{{ route('instructor.quizzes.show', $quiz) }}" class="hover:text-brand-700 dark:hover:text-brand-300">&larr; {{ $quiz->title }}</a>
    </p>

    @include('instructor.quizzes.partials.form')

    <div class="flex items-center gap-3 max-w-3xl justify-end">
        <a href="{{ route('instructor.quizzes.show', $quiz) }}" class="px-5 py-2.5 rounded-xl bg-white dark:bg-neutral-900 border border-gray-300 dark:border-neutral-700 text-sm font-semibold text-gray-700 dark:text-neutral-200 hover:bg-gray-50 dark:hover:bg-neutral-800">Hủy</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-semibold shadow-sm hover:bg-violet-700">Lưu quiz</button>
    </div>
</form>
@endsection
