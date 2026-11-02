@extends('layouts.instructor')

@section('title', 'Quản lý quiz')
@section('page_title', 'Quản lý câu hỏi quiz')

@section('content')
@php
    $canEdit = auth()->user()->can('update', $quiz->course);
@endphp

<div class="space-y-6">
    <p class="text-sm text-gray-500 dark:text-neutral-400">
        <a href="{{ route('instructor.courses.show', $quiz->course) }}" class="hover:text-brand-700 dark:hover:text-brand-300">&larr; {{ $quiz->course->title }}</a>
    </p>

    <!-- Thông tin quiz -->
    <div class="bg-white dark:bg-neutral-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold text-violet-600 dark:text-violet-400 uppercase">{{ $quiz->chapter->title ?? 'Quiz chung của khóa học' }}</p>
            <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100 mt-1">{{ $quiz->title }}</h2>
            @if($quiz->description)
                <p class="text-sm text-gray-600 dark:text-neutral-300 mt-2 whitespace-pre-line">{{ $quiz->description }}</p>
            @endif
            <p class="text-xs text-gray-500 dark:text-neutral-400 mt-2">{{ $quiz->questions->count() }} câu hỏi · {{ $quiz->attempts_count }} lượt làm bài</p>
        </div>
        @if($canEdit)
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('instructor.quizzes.edit', $quiz) }}" class="px-4 py-2 rounded-xl bg-white dark:bg-neutral-900 border border-gray-300 dark:border-neutral-700 text-sm font-semibold text-gray-700 dark:text-neutral-200 hover:bg-gray-50 dark:hover:bg-neutral-800">Sửa quiz</a>
                <form method="POST" action="{{ route('instructor.quizzes.destroy', $quiz) }}" onsubmit="return confirm('Xóa quiz này cùng toàn bộ câu hỏi và lịch sử làm bài?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 text-sm font-semibold hover:bg-rose-100 dark:hover:bg-rose-500/15">Xóa quiz</button>
                </form>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
        <!-- Danh sách câu hỏi -->
        <div class="xl:col-span-3 space-y-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-neutral-100">Danh sách câu hỏi</h3>

            @forelse($quiz->questions as $question)
                <div class="bg-white dark:bg-neutral-900 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-semibold text-gray-900 dark:text-neutral-100">
                            <span class="text-violet-600 dark:text-violet-400">Câu {{ $loop->iteration }}.</span>
                            <span class="whitespace-pre-line">{{ $question->question_text }}</span>
                        </p>
                        @if($canEdit)
                            <div class="flex items-center gap-1 shrink-0">
                                <a href="{{ route('instructor.questions.edit', $question) }}" class="px-2.5 py-1 rounded-lg text-xs font-semibold text-brand-700 dark:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-500/15">Sửa</a>
                                <form method="POST" action="{{ route('instructor.questions.destroy', $question) }}" onsubmit="return confirm('Xóa câu hỏi này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-500/15">Xóa</button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <ul class="mt-3 space-y-1.5">
                        @foreach($question->answers as $answer)
                            <li class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm {{ $answer->is_correct ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-300 font-medium' : 'bg-gray-50 dark:bg-neutral-800/40 text-gray-700 dark:text-neutral-200' }}">
                                <span class="font-bold w-5">{{ chr(65 + $loop->index) }}.</span>
                                <span class="flex-1">{{ $answer->answer_text }}</span>
                                @if($answer->is_correct)
                                    <span class="text-xs font-semibold">✓ Đáp án đúng</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    @if($question->explanation)
                        <p class="mt-3 text-xs text-gray-500 dark:text-neutral-400"><strong>Giải thích:</strong> {{ $question->explanation }}</p>
                    @endif
                </div>
            @empty
                <div class="bg-white dark:bg-neutral-900 p-10 rounded-2xl shadow-sm border border-dashed border-gray-300 dark:border-neutral-700 text-center text-gray-500 dark:text-neutral-400">
                    Quiz chưa có câu hỏi nào.
                </div>
            @endforelse
        </div>

        <!-- Form thêm câu hỏi -->
        @if($canEdit)
            <div class="xl:col-span-2">
                <form method="POST" action="{{ route('instructor.quizzes.questions.store', $quiz) }}" class="bg-white dark:bg-neutral-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 space-y-5 xl:sticky xl:top-4">
                    @csrf
                    <h3 class="text-base font-bold text-gray-900 dark:text-neutral-100">Thêm câu hỏi mới</h3>

                    @include('instructor.questions.partials.form', ['question' => new \App\Models\Question()])

                    <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-semibold shadow-sm hover:bg-violet-700">Thêm câu hỏi</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
