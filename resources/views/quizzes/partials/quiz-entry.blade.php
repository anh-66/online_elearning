{{-- Mục Quiz trắc nghiệm cuối chương trong Danh sách bài học (TV4) --}}
@php
    $bestAttempt = $quiz->attempts()
        ->where('user_id', auth()->id())
        ->latest('score')
        ->first();
    $questionsTotal = $quiz->questions()->count();
@endphp

<div class="group flex items-center justify-between gap-2.5 px-3 py-2.5 rounded-xl border transition-all text-xs
     {{ $bestAttempt && $bestAttempt->is_passed 
        ? 'bg-emerald-50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40 hover:border-emerald-300 dark:hover:border-emerald-700/60' 
        : ($bestAttempt ? 'bg-rose-50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-900/40 hover:border-rose-300 dark:hover:border-rose-800/60' : 'bg-slate-50/80 dark:bg-[#0d1117]/60 border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700') }}">
    
    {{-- Icon Quiz --}}
    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 border
         {{ $bestAttempt && $bestAttempt->is_passed 
            ? 'bg-emerald-500/15 border-emerald-500/30 text-emerald-600 dark:text-emerald-400' 
            : ($bestAttempt ? 'bg-rose-500/15 border-rose-500/30 text-rose-600 dark:text-rose-400' : 'bg-[#019D91]/10 border-[#019D91]/25 text-[#019D91] dark:text-[#02b3a5]') }}">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
    </div>

    {{-- Nội dung tiêu đề & Trạng thái --}}
    <div class="min-w-0 flex-1">
        <a href="{{ route('quiz.show', $quiz) }}"
           class="font-medium text-[#1D1D1F] dark:text-white hover:text-[#019D91] dark:hover:text-[#02b3a5] transition truncate block leading-snug"
           title="{{ $quiz->title }}">
            {{ $quiz->title }}
        </a>
        <div class="flex items-center gap-1.5 mt-0.5 text-[11px] text-[#86868B] dark:text-slate-400">
            <span>{{ $questionsTotal }} câu hỏi</span>
            @if($bestAttempt)
                <span>·</span>
                <span class="whitespace-nowrap px-1.5 py-0.2 rounded text-[10px] font-bold shrink-0
                     {{ $bestAttempt->is_passed ? 'bg-emerald-500/15 text-emerald-700 dark:text-emerald-300' : 'bg-rose-500/15 text-rose-700 dark:text-rose-300' }}">
                    {{ $bestAttempt->is_passed ? 'Đạt' : 'Chưa đạt' }} ({{ $bestAttempt->score }}%)
                </span>
            @endif
        </div>
    </div>

    {{-- Nhóm nút hành động tinh gọn --}}
    <div class="flex items-center gap-1 shrink-0">
        @if($bestAttempt)
            <a href="{{ route('quiz.history', $quiz) }}"
               class="p-1.5 text-slate-400 hover:text-[#1D1D1F] dark:hover:text-white rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 transition"
               title="Xem lịch sử làm bài">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </a>
        @endif
        <a href="{{ route('quiz.show', $quiz) }}"
           class="px-2.5 py-1 text-[11px] font-semibold rounded-lg text-white transition whitespace-nowrap shadow-sm
                  {{ $bestAttempt && $bestAttempt->is_passed 
                     ? 'bg-emerald-600 hover:bg-emerald-500' 
                     : 'bg-[#019D91] hover:bg-[#008b80]' }}">
            {{ $bestAttempt ? 'Làm lại' : 'Làm bài' }}
        </a>
    </div>
</div>
