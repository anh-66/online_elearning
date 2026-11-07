@extends('layouts.public')

@section('title', 'Làm bài: ' . $quiz->title)

@section('content')
@php
    $questionsCount = $quiz->questions->count();
    $timeLimitMinutes = (int) ($quiz->time_limit ?: 15);
@endphp

<div class="bg-[#F8F9FA] dark:bg-[#0d1117] min-h-[calc(100vh-4.5rem)] py-8 transition-colors"
     x-data="quizPlayer(@js($quiz->questions->pluck('id')->values()), {{ $timeLimitMinutes }})"
     x-cloak>
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        {{-- ===== HEADER QUIZ ===== --}}
        <div class="flex items-center justify-between mb-6 pb-4">
            <div class="flex items-center gap-3.5 min-w-0">
                <a href="{{ route('learning.player', $quiz->course->slug) }}"
                   class="w-10 h-10 rounded-2xl bg-white dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-[#21262d] transition shrink-0 shadow-sm"
                   title="Quay lại phòng học"
                   @click="if (!confirmExit()) $event.preventDefault()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="min-w-0">
                    <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white truncate">
                        Quiz: {{ $quiz->title }}
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        {{ $quiz->chapter ? $quiz->chapter->title : $quiz->course->title }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                {{-- Đồng hồ đếm ngược --}}
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] text-xs sm:text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span :class="remaining <= 60 ? 'text-red-500 font-bold' : ''" x-text="formatTime()">15:00</span>
                </div>

                {{-- Nút Thoát --}}
                <a href="{{ route('learning.player', $quiz->course->slug) }}"
                   @click="if (!confirmExit()) $event.preventDefault()"
                   class="text-xs sm:text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white px-2 py-1 transition">
                    Thoát
                </a>
            </div>
        </div>

        {{-- ===== THANH TIẾN TRÌNH ===== --}}
        <div class="mb-8">
            <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">
                <span>TIẾN TRÌNH: <span class="text-gray-800 dark:text-gray-200" x-text="String(currentIndex + 1).padStart(2, '0')">01</span> / {{ sprintf('%02d', $questionsCount) }} CÂU</span>
                <span class="text-gray-800 dark:text-gray-200" x-text="progressPercent() + '%'">0%</span>
            </div>
            <div class="w-full h-1.5 bg-gray-200 dark:bg-[#21262d] rounded-full overflow-hidden">
                <div class="h-full bg-black dark:bg-white transition-all duration-300"
                     :style="`width: ${progressPercent()}%`"></div>
            </div>
        </div>

        @if($quiz->questions->isEmpty())
            <div class="bg-white dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] rounded-3xl p-12 text-center shadow-sm">
                <p class="text-base font-semibold text-gray-800 dark:text-gray-200">Bài quiz này chưa có câu hỏi nào.</p>
                <p class="text-xs text-gray-500 mt-1">Vui lòng quay lại sau khi giảng viên cập nhật nội dung.</p>
                <a href="{{ route('learning.player', $quiz->course->slug) }}" class="mt-6 inline-flex px-6 py-2.5 rounded-xl bg-black text-white text-xs font-semibold">
                    Quay lại phòng học
                </a>
            </div>
        @else
            {{-- Form nộp bài --}}
            <form x-ref="quizForm" action="{{ route('quiz.submit', $quiz) }}" method="POST">
                @csrf

                {{-- Vòng lặp từng câu hỏi (chỉ hiển thị câu currentIndex) --}}
                @foreach($quiz->questions as $qIndex => $question)
                    <div x-show="currentIndex === {{ $qIndex }}"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="bg-white dark:bg-[#161b22] rounded-3xl p-6 sm:p-10 border border-gray-100 dark:border-[#30363d] shadow-sm mb-8">

                        {{-- Badge Số thứ tự câu --}}
                        <div class="inline-block px-3 py-1 rounded-md bg-gray-100 dark:bg-[#21262d] text-gray-600 dark:text-gray-300 text-[11px] font-bold uppercase tracking-wider mb-4">
                            CÂU HỎI {{ sprintf('%02d', $qIndex + 1) }}
                        </div>

                        {{-- Tiêu đề câu hỏi --}}
                        <h2 class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-white leading-snug mb-8 whitespace-pre-line">
                            {{ $question->question_text }}
                        </h2>

                        {{-- Danh sách các lựa chọn đáp án --}}
                        <div class="space-y-3.5">
                            @php
                                $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
                            @endphp
                            @foreach($question->answers as $aIndex => $answer)
                                @php
                                    $letter = $letters[$aIndex] ?? chr(65 + $aIndex);
                                @endphp
                                <label class="w-full flex items-center gap-4 p-4 sm:p-5 rounded-2xl border cursor-pointer transition-all duration-150 select-none"
                                       :class="answers[{{ $question->id }}] === {{ $answer->id }}
                                            ? 'bg-black text-white border-black dark:bg-white dark:text-black dark:border-white shadow-md'
                                            : 'bg-white dark:bg-[#161b22] text-gray-800 dark:text-gray-200 border-gray-200 dark:border-[#30363d] hover:bg-gray-50 dark:hover:bg-[#21262d]'">
                                    
                                    <input type="radio"
                                           name="answers[{{ $question->id }}]"
                                           value="{{ $answer->id }}"
                                           class="sr-only"
                                           @change="selectAnswer({{ $question->id }}, {{ $answer->id }})">

                                    {{-- Khối chữ cái A, B, C, D --}}
                                    <span class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm shrink-0 transition-colors"
                                          :class="answers[{{ $question->id }}] === {{ $answer->id }}
                                                ? 'bg-neutral-800 text-white dark:bg-neutral-200 dark:text-black'
                                                : 'bg-gray-100 dark:bg-[#21262d] text-gray-700 dark:text-gray-300'">
                                        {{ $letter }}
                                    </span>

                                    {{-- Nội dung đáp án --}}
                                    <span class="text-sm sm:text-base font-normal leading-relaxed flex-1">
                                        {{ $answer->answer_text }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                {{-- ===== FOOTER ĐIỀU HƯỚNG CÂU HỎI ===== --}}
                <div class="flex items-center justify-between gap-4 mb-8">
                    {{-- Nút Câu trước --}}
                    <button type="button"
                            @click="prevQuestion()"
                            :disabled="currentIndex === 0"
                            class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold transition px-3 py-2 rounded-xl"
                            :class="currentIndex === 0 ? 'text-gray-300 dark:text-gray-700 cursor-not-allowed' : 'text-gray-600 dark:text-gray-300 hover:text-black dark:hover:text-white'">
                        <span>&larr;</span>
                        <span>Câu trước</span>
                    </button>

                    <div class="flex items-center gap-3">
                        {{-- Nút Đánh dấu xem lại --}}
                        <button type="button"
                                @click="toggleFlag(currentQuestionId())"
                                class="w-10 h-10 rounded-2xl border flex items-center justify-center transition"
                                :class="flags[currentQuestionId()]
                                    ? 'bg-amber-50 border-amber-300 text-amber-600 dark:bg-amber-950/40 dark:border-amber-700'
                                    : 'bg-white dark:bg-[#161b22] border-gray-200 dark:border-[#30363d] text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                                title="Đánh dấu câu hỏi để xem lại">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        {{-- Nút Tiếp theo / Nộp bài --}}
                        <template x-if="currentIndex < totalQuestions - 1">
                            <button type="button"
                                    @click="nextQuestion()"
                                    class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-black dark:bg-white text-white dark:text-black text-xs sm:text-sm font-semibold hover:opacity-90 transition shadow-sm">
                                <span>Tiếp theo</span>
                                <span>&rarr;</span>
                            </button>
                        </template>

                        <template x-if="currentIndex === totalQuestions - 1">
                            <button type="button"
                                    @click="confirmSubmit()"
                                    class="inline-flex items-center gap-2 px-7 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs sm:text-sm font-semibold transition shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Nộp bài &amp; Chấm điểm</span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- ===== BĂNG SỐ PHÂN TRANG CÂU HỎI Ở ĐÁY ===== --}}
                <div class="flex flex-wrap items-center justify-center gap-2 pt-4">
                    @foreach($quiz->questions as $pIndex => $q)
                        <button type="button"
                                @click="jumpToQuestion({{ $pIndex }})"
                                class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl text-xs sm:text-sm font-semibold flex items-center justify-center transition-all relative"
                                :class="{
                                    'border-2 border-black dark:border-white bg-white dark:bg-[#161b22] text-black dark:text-white shadow-sm': currentIndex === {{ $pIndex }},
                                    'bg-black text-white dark:bg-white dark:text-black': currentIndex !== {{ $pIndex }} && answers[{{ $q->id }}],
                                    'bg-gray-100 dark:bg-[#21262d] text-gray-500 dark:text-gray-400 hover:bg-gray-200': currentIndex !== {{ $pIndex }} && !answers[{{ $q->id }}]
                                }">
                            <span>{{ $pIndex + 1 }}</span>
                            <template x-if="flags[{{ $q->id }}]">
                                <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-amber-500 ring-2 ring-white dark:ring-[#0d1117]"></span>
                            </template>
                        </button>
                    @endforeach
                </div>
            </form>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
    function quizPlayer(questionIds, timeLimitMinutes) {
        return {
            questionIds: questionIds,
            totalQuestions: questionIds.length,
            currentIndex: 0,
            answers: {},
            flags: {},
            remaining: timeLimitMinutes * 60,

            init() {
                // Đọc các đáp án đã tick nếu form re-render
                this.$refs.quizForm?.querySelectorAll('input[type=radio]:checked').forEach((input) => {
                    const match = input.name.match(/\d+/);
                    if (match) {
                        this.answers[match[0]] = parseInt(input.value);
                    }
                });

                // Đếm ngược thời gian làm bài
                if (this.remaining > 0 && this.$refs.quizForm) {
                    const timer = setInterval(() => {
                        this.remaining--;
                        if (this.remaining <= 0) {
                            clearInterval(timer);
                            alert('Hết giờ làm bài! Hệ thống sẽ tự động nộp bài làm của bạn.');
                            this.$refs.quizForm.submit();
                        }
                    }, 1000);
                }
            },

            currentQuestionId() {
                return this.questionIds[this.currentIndex];
            },

            selectAnswer(qId, aId) {
                this.answers[qId] = aId;
            },

            nextQuestion() {
                if (this.currentIndex < this.totalQuestions - 1) {
                    this.currentIndex++;
                }
            },

            prevQuestion() {
                if (this.currentIndex > 0) {
                    this.currentIndex--;
                }
            },

            jumpToQuestion(index) {
                if (index >= 0 && index < this.totalQuestions) {
                    this.currentIndex = index;
                }
            },

            toggleFlag(qId) {
                this.flags[qId] = !this.flags[qId];
            },

            answeredCount() {
                return Object.keys(this.answers).length;
            },

            progressPercent() {
                if (this.totalQuestions === 0) return 0;
                return Math.round((this.answeredCount() / this.totalQuestions) * 100);
            },

            formatTime() {
                const minutes = String(Math.floor(this.remaining / 60)).padStart(2, '0');
                const seconds = String(this.remaining % 60).padStart(2, '0');
                return `${minutes}:${seconds}`;
            },

            confirmSubmit() {
                const missing = this.totalQuestions - this.answeredCount();
                if (missing > 0) {
                    if (!confirm(`Bạn còn ${missing} câu hỏi chưa trả lời. Bạn có chắc chắn muốn nộp bài ngay bây giờ?`)) {
                        return;
                    }
                }
                this.$refs.quizForm.submit();
            },

            confirmExit() {
                return confirm('Bạn có chắc chắn muốn thoát? Bài làm chưa nộp sẽ không được lưu điểm.');
            }
        };
    }
</script>
@endpush
