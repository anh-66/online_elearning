@extends('layouts.public')

@section('title', 'Kết quả: ' . $quiz->title)

@section('content')
<div class="bg-[#F8F9FA] dark:bg-[#0d1117] min-h-[calc(100vh-4.5rem)] py-8 transition-colors">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 space-y-8">

        {{-- ===== BREADCRUMB ===== --}}
        <nav class="flex flex-wrap items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">
            <a href="{{ route('courses.index') }}" class="hover:text-black dark:hover:text-white transition">KHÓA HỌC</a>
            <span>&rsaquo;</span>
            <a href="{{ route('courses.show', $quiz->course->slug) }}" class="hover:text-black dark:hover:text-white transition truncate max-w-xs">{{ strtoupper($quiz->course->title) }}</a>
            <span>&rsaquo;</span>
            <span class="text-black dark:text-white">KẾT QUẢ TRẮC NGHIỆM</span>
        </nav>

        {{-- ===== HÀNG THỐNG KÊ TRÊN CÙNG ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            {{-- Cột trái: Card đen tròn điểm số nổi bật --}}
            <div class="lg:col-span-5 bg-black text-white dark:bg-[#161b22] dark:border dark:border-[#30363d] rounded-3xl p-8 sm:p-10 flex flex-col justify-between shadow-xl relative overflow-hidden">
                <div class="space-y-6 text-center">
                    {{-- Vòng tròn điểm số trung tâm --}}
                    <div class="w-32 h-32 rounded-full border-4 border-white/90 dark:border-white/80 mx-auto flex flex-col items-center justify-center p-2 shadow-inner">
                        <span class="text-4xl font-extrabold tracking-tight leading-none">{{ $attempt->total_correct }}</span>
                        <div class="w-12 h-px bg-white/40 my-1"></div>
                        <span class="text-sm font-medium text-white/70">/ {{ $attempt->total_questions }}</span>
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">
                            {{ $attempt->is_passed ? 'Xuất sắc, ' . (auth()->user()->name ?? 'bạn') . '!' : 'Cố gắng lên, ' . (auth()->user()->name ?? 'bạn') . '!' }}
                        </h1>
                        <p class="text-xs text-neutral-300 dark:text-gray-400 mt-2 leading-relaxed">
                            {{ $attempt->is_passed ? 'Bạn đã hoàn thành bài kiểm tra cuối chương với kết quả ấn tượng.' : 'Bạn chưa đạt yêu cầu tối thiểu (80%) để vượt qua bài kiểm tra. Hãy xem lại câu hỏi và thử lại nhé!' }}
                        </p>
                    </div>
                </div>

                {{-- 2 thông số đáy card --}}
                <div class="grid grid-cols-2 gap-4 pt-6 border-t border-neutral-800 dark:border-neutral-700 text-center mt-6">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-neutral-400">ĐỘ CHÍNH XÁC</p>
                        <p class="text-xl font-bold mt-0.5 text-white">{{ $attempt->score }}%</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-neutral-400">KẾT QUẢ</p>
                        <p class="text-xl font-bold mt-0.5 {{ $attempt->is_passed ? 'text-emerald-400' : 'text-rose-400' }}">
                            {{ $attempt->is_passed ? 'ĐẠT' : 'CHƯA ĐẠT' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Cột phải: Phân tích & Cụm nút hành động --}}
            <div class="lg:col-span-7 bg-white dark:bg-[#161b22] rounded-3xl p-6 sm:p-8 border border-gray-100 dark:border-[#30363d] shadow-sm flex flex-col justify-between space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">
                        Tổng quan bài kiểm tra
                    </h2>

                    <div class="space-y-4 text-xs">
                        <div>
                            <div class="flex justify-between items-center mb-1 font-semibold text-gray-700 dark:text-gray-300">
                                <span>Tỉ lệ câu trả lời đúng</span>
                                <span>{{ $attempt->score }}%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 dark:bg-[#21262d] rounded-full overflow-hidden">
                                <div class="h-full bg-black dark:bg-white rounded-full transition-all duration-500" style="width: {{ $attempt->score }}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-1 font-semibold text-gray-700 dark:text-gray-300">
                                <span>Mức điểm yêu cầu tối thiểu</span>
                                <span>{{ \App\Models\QuizAttempt::PASSING_SCORE }}%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 dark:bg-[#21262d] rounded-full overflow-hidden">
                                <div class="h-full bg-gray-400 dark:bg-gray-600 rounded-full" style="width: {{ \App\Models\QuizAttempt::PASSING_SCORE }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-6 pt-6 border-t border-gray-100 dark:border-[#30363d] text-xs">
                        <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#0d1117]">
                            <span class="text-gray-400 block text-[10px] uppercase font-bold">Số câu đúng</span>
                            <span class="text-base font-bold text-emerald-600 dark:text-emerald-400">{{ $attempt->total_correct }} câu</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#0d1117]">
                            <span class="text-gray-400 block text-[10px] uppercase font-bold">Số câu sai</span>
                            <span class="text-base font-bold text-rose-600 dark:text-rose-400">{{ max(0, $attempt->total_questions - $attempt->total_correct) }} câu</span>
                        </div>
                        <div class="p-3 rounded-2xl bg-gray-50 dark:bg-[#0d1117] col-span-2 sm:col-span-1">
                            <span class="text-gray-400 block text-[10px] uppercase font-bold">Ngày làm</span>
                            <span class="text-base font-bold text-gray-800 dark:text-gray-200">{{ $attempt->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Nhóm nút hành động --}}
                <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-gray-100 dark:border-[#30363d]">
                    <a href="#analysis-section"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-[#30363d] text-gray-700 dark:text-gray-300 text-xs font-semibold hover:bg-gray-50 dark:hover:bg-[#21262d] transition">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>Xem lại câu hỏi</span>
                    </a>

                    <a href="{{ route('quiz.show', $quiz) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-[#30363d] text-gray-700 dark:text-gray-300 text-xs font-semibold hover:bg-gray-50 dark:hover:bg-[#21262d] transition">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Thử lại Quiz</span>
                    </a>

                    <a href="{{ route('learning.player', $quiz->course->slug) }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-black dark:bg-white text-white dark:text-black text-xs font-semibold hover:opacity-90 transition ml-auto shadow-sm">
                        <span>Tiếp tục bài học</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ===== PHẦN PHÂN TÍCH TỪNG CÂU HỎI ===== --}}
        <section id="analysis-section" class="space-y-4 pt-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                Phân tích từng câu hỏi
            </h2>

            @forelse($quiz->questions as $index => $question)
                @php
                    $selectedAnswerId = $attempt->selectedAnswerFor($question);
                    $correctAnswer = $question->answers->firstWhere('is_correct', true);
                    $selectedAnswer = $question->answers->firstWhere('id', $selectedAnswerId);

                    // Nhận diện câu đúng
                    if ($attempt->score === 100) {
                        $isCorrect = true;
                        $selectedAnswer = $correctAnswer;
                    } else {
                        $isCorrect = $correctAnswer && $selectedAnswerId === $correctAnswer->id;
                    }
                @endphp

                <article class="bg-white dark:bg-[#161b22] rounded-2xl p-6 sm:p-7 border border-gray-100 dark:border-[#30363d] shadow-sm space-y-3 transition">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3 min-w-0">
                            <span class="text-sm font-extrabold text-gray-400 dark:text-gray-500 pt-0.5 shrink-0">
                                {{ sprintf('%02d', $index + 1) }}
                            </span>
                            <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white leading-snug">
                                {{ $question->question_text }}
                            </h3>
                        </div>

                        {{-- Badge Trạng thái: ĐÚNG / SAI / BỎ TRỐNG --}}
                        <div class="shrink-0">
                            @if($isCorrect)
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>ĐÚNG</span>
                                </span>
                            @elseif($selectedAnswer)
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-3 py-1 rounded-full bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    <span>SAI</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-3 py-1 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                    <span>—</span>
                                    <span>BỎ TRỐNG</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Thông tin đáp án của bạn & đáp án đúng --}}
                    <div class="pl-7 space-y-1 text-xs sm:text-sm">
                        <p class="text-gray-600 dark:text-gray-300">
                            <span class="text-gray-400">Đáp án của bạn:</span>
                            @if($selectedAnswer)
                                <span class="font-semibold {{ $isCorrect ? 'text-gray-900 dark:text-white' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ $selectedAnswer->answer_text }}
                                </span>
                            @else
                                <span class="text-gray-400 italic">Không chọn đáp án nào</span>
                            @endif
                        </p>

                        @if(! $isCorrect && $correctAnswer)
                            <p class="text-emerald-600 dark:text-emerald-400 font-medium">
                                <span class="text-gray-400 font-normal">Đáp án đúng:</span>
                                <span class="font-semibold">{{ $correctAnswer->answer_text }}</span>
                            </p>
                        @endif
                    </div>

                    {{-- Khung giải thích / lưu ý nếu có --}}
                    @if(filled($question->explanation))
                        <div class="ml-7 mt-3 p-4 rounded-xl bg-gray-50 dark:bg-[#0d1117] border-l-4 border-black dark:border-white text-xs leading-relaxed text-gray-700 dark:text-gray-300">
                            <span class="font-bold uppercase tracking-wider text-[10px] text-gray-500 block mb-1">GIẢI THÍCH:</span>
                            <p>{{ $question->explanation }}</p>
                        </div>
                    @endif
                </article>
            @empty
                <div class="p-8 text-center text-gray-400 text-sm">
                    Không có câu hỏi nào để hiển thị.
                </div>
            @endforelse
        </section>

    </div>
</div>
@endsection
