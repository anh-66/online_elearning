@extends('layouts.instructor')

@section('title', 'Sửa câu hỏi')
@section('page_title', 'Sửa câu hỏi trắc nghiệm')

@section('content')
<div class="space-y-6 max-w-3xl">
    <p class="text-sm text-gray-500">
        <a href="{{ route('instructor.quizzes.show', $quiz) }}" class="hover:text-indigo-600">&larr; {{ $quiz->title }}</a>
    </p>

    <form method="POST" action="{{ route('instructor.questions.update', $question) }}" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-5">
        @csrf
        @method('PUT')

        @include('instructor.questions.partials.form')

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('instructor.quizzes.show', $quiz) }}" class="px-5 py-2.5 rounded-xl bg-white border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50">Hủy</a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-semibold shadow-sm hover:bg-violet-700">Lưu câu hỏi</button>
        </div>
    </form>
</div>
@endsection
