@extends('layouts.instructor')

@section('title', 'Quản lý quiz')
@section('page_title', 'Quản lý câu hỏi quiz')

@section('content')
@php
    $canEdit = auth()->user()->can('update', $quiz->course);
@endphp

<div class="space-y-6">
    <p class="text-sm text-gray-500">
        <a href="{{ route('instructor.courses.show', $quiz->course) }}" class="hover:text-indigo-600">&larr; {{ $quiz->course->title }}</a>
    </p>

    <!-- Thông tin quiz -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold text-violet-600 uppercase">{{ $quiz->chapter->title ?? 'Quiz chung của khóa học' }}</p>
            <h2 class="text-xl font-bold text-gray-900 mt-1">{{ $quiz->title }}</h2>
            @if($quiz->description)
                <p class="text-sm text-gray-600 mt-2 whitespace-pre-line">{{ $quiz->description }}</p>
            @endif
            <p class="text-xs text-gray-500 mt-2">{{ $quiz->questions->count() }} câu hỏi · {{ $quiz->attempts_count }} lượt làm bài</p>
        </div>
        @if($canEdit)
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('instructor.quizzes.edit', $quiz) }}" class="px-4 py-2 rounded-xl bg-white border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50">Sửa quiz</a>
                <form method="POST" action="{{ route('instructor.quizzes.destroy', $quiz) }}" onsubmit="return confirm('Xóa quiz này cùng toàn bộ câu hỏi và lịch sử làm bài?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-50 text-rose-700 text-sm font-semibold hover:bg-rose-100">Xóa quiz</button>
                </form>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
        <!-- Danh sách câu hỏi -->
        <div class="xl:col-span-3 space-y-4">
            <h3 class="text-lg font-bold text-gray-900">Danh sách câu hỏi</h3>

            @forelse($quiz->questions as $question)
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-semibold text-gray-900">
                            <span class="text-violet-600">Câu {{ $loop->iteration }}.</span>
                            <span class="whitespace-pre-line">{{ $question->question_text }}</span>
                        </p>
                        @if($canEdit)
                            <div class="flex items-center gap-1 shrink-0">
                                <a href="{{ route('instructor.questions.edit', $question) }}" class="px-2.5 py-1 rounded-lg text-xs font-semibold text-indigo-600 hover:bg-indigo-50">Sửa</a>
                                <form method="POST" action="{{ route('instructor.questions.destroy', $question) }}" onsubmit="return confirm('Xóa câu hỏi này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold text-rose-600 hover:bg-rose-50">Xóa</button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <ul class="mt-3 space-y-1.5">
                        @foreach($question->answers as $answer)
                            <li class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm {{ $answer->is_correct ? 'bg-emerald-50 text-emerald-800 font-medium' : 'bg-gray-50 text-gray-700' }}">
                                <span class="font-bold w-5">{{ chr(65 + $loop->index) }}.</span>
                                <span class="flex-1">{{ $answer->answer_text }}</span>
                                @if($answer->is_correct)
                                    <span class="text-xs font-semibold">✓ Đáp án đúng</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    @if($question->explanation)
                        <p class="mt-3 text-xs text-gray-500"><strong>Giải thích:</strong> {{ $question->explanation }}</p>
                    @endif
                </div>
            @empty
                <div class="bg-white p-10 rounded-2xl shadow-sm border border-dashed border-gray-300 text-center text-gray-500">
                    Quiz chưa có câu hỏi nào.
                </div>
            @endforelse
        </div>

        <!-- Form thêm câu hỏi -->
        @if($canEdit)
            <div class="xl:col-span-2">
                <form method="POST" action="{{ route('instructor.quizzes.questions.store', $quiz) }}" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-5 xl:sticky xl:top-4">
                    @csrf
                    <h3 class="text-base font-bold text-gray-900">Thêm câu hỏi mới</h3>

                    @include('instructor.questions.partials.form', ['question' => new \App\Models\Question()])

                    <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-semibold shadow-sm hover:bg-violet-700">Thêm câu hỏi</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
