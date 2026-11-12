@extends('layouts.public')

@section('title', 'Lịch sử làm bài: ' . $quiz->title)

@section('content')
<div class="bg-[#F8F9FA] dark:bg-[#0d1117] min-h-[calc(100vh-4.5rem)] py-8 transition-colors">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 space-y-8">

        {{-- ===== HEADER & BREADCRUMB ===== --}}
        <div>
            <nav class="flex flex-wrap items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-3">
                <a href="{{ route('learning.player', $quiz->course->slug) }}" class="hover:text-black dark:hover:text-white transition">{{ strtoupper($quiz->course->title) }}</a>
                <span>&rsaquo;</span>
                <a href="{{ route('quiz.show', $quiz) }}" class="hover:text-black dark:hover:text-white transition">{{ strtoupper($quiz->title) }}</a>
                <span>&rsaquo;</span>
                <span class="text-black dark:text-white">LỊCH SỬ</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Lịch sử làm bài
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">
                Theo dõi sự tiến bộ và xem lại các kết quả trắc nghiệm của bạn.
            </p>
        </div>

        {{-- ===== 3 THẺ THỐNG KÊ ĐẦU TRANG ===== --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            {{-- Thẻ 1: Tổng quiz đã làm --}}
            <div class="bg-white dark:bg-[#161b22] rounded-2xl p-6 border border-gray-100 dark:border-[#30363d] shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-2">
                    TỔNG LẦN LÀM BÀI
                </span>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white">
                        {{ $totalAttempts ?? $attempts->total() }}
                    </span>
                    <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        ghi nhận
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-2">Tổng số lần bạn đã thử sức bài quiz này</p>
            </div>

            {{-- Thẻ 2: Điểm trung bình --}}
            <div class="bg-white dark:bg-[#161b22] rounded-2xl p-6 border border-gray-100 dark:border-[#30363d] shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-2">
                    ĐIỂM TRUNG BÌNH
                </span>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white">
                        {{ number_format($avgScore ?? 0, 1) }}
                    </span>
                    <span class="text-sm font-semibold text-gray-400">/ 100</span>
                </div>
                <p class="text-xs text-gray-400 mt-2">Tỉ lệ chính xác trung bình qua các lần làm</p>
            </div>

            {{-- Thẻ 3: Điểm cao nhất --}}
            <div class="bg-white dark:bg-[#161b22] rounded-2xl p-6 border border-gray-100 dark:border-[#30363d] shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-2">
                    ĐIỂM CAO NHẤT
                </span>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl sm:text-4xl font-extrabold text-emerald-600 dark:text-emerald-400">
                        {{ $bestScore ?? 0 }}%
                    </span>
                    @if(($bestScore ?? 0) >= \App\Models\QuizAttempt::PASSING_SCORE)
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-300">
                            Đã đạt
                        </span>
                    @endif
                </div>
                <p class="text-xs text-gray-400 mt-2">Kỷ lục điểm số cao nhất của bạn</p>
            </div>
        </div>

        {{-- ===== THANH TÌM KIẾM & BỘ LỌC ===== --}}
        <div class="bg-white dark:bg-[#161b22] rounded-2xl p-5 border border-gray-100 dark:border-[#30363d] shadow-sm">
            <form action="{{ route('quiz.history', $quiz) }}" method="GET" class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                    {{-- Lọc Trạng thái (Combobox Animate-UI) --}}
                    @php
                        $statusOptions = [
                            '' => 'Trạng thái: Tất cả',
                            'passed' => 'Đã hoàn thành (Đạt)',
                            'failed' => 'Chưa đạt (< 80%)',
                        ];
                        $initialStatus = request('status', '');
                        $initialStatusLabel = $statusOptions[$initialStatus] ?? 'Trạng thái: Tất cả';
                    @endphp
                    <div class="relative min-w-[200px]"
                         x-data="{
                             open: false,
                             selectedValue: '{{ $initialStatus }}',
                             selectedLabel: '{{ $initialStatusLabel }}',
                             options: [
                                 { value: '', label: 'Trạng thái: Tất cả' },
                                 { value: 'passed', label: 'Đã hoàn thành (Đạt)' },
                                 { value: 'failed', label: 'Chưa đạt (< 80%)' }
                             ],
                             select(val, label) {
                                 this.selectedValue = val;
                                 this.selectedLabel = label;
                                 this.open = false;
                                 this.$nextTick(() => {
                                     this.$el.closest('form').submit();
                                 });
                             }
                         }">
                        <input type="hidden" name="status" :value="selectedValue">

                        <button type="button"
                                @click="open = !open"
                                :aria-expanded="open"
                                data-slot="combobox-trigger"
                                class="w-full text-xs font-medium rounded-xl border border-gray-200 dark:border-[#30363d] bg-gray-50 dark:bg-[#0d1117] text-gray-700 dark:text-gray-300 py-2 px-3.5 flex items-center justify-between text-left focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 transition-all cursor-pointer select-none group/combobox-trigger shadow-xs">
                            <span x-text="selectedLabel" class="truncate font-medium"></span>
                            <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500 transition-transform duration-200 group-hover/combobox-trigger:text-gray-700 dark:group-hover/combobox-trigger:text-gray-300 shrink-0 ml-2 pointer-events-none"
                                 :class="open ? 'rotate-180 text-gray-700 dark:text-gray-300' : ''"
                                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open"
                             x-cloak
                             @click.outside="open = false"
                             @keydown.escape.window="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1 scale-98"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 -translate-y-1 scale-98"
                             data-slot="combobox-content"
                             class="absolute left-0 right-0 top-full mt-1.5 z-50 min-w-[210px] rounded-xl border border-gray-200 dark:border-[#30363d] bg-white dark:bg-[#161b22] shadow-xl ring-1 ring-black/5 dark:ring-white/5 p-1 overflow-hidden space-y-0.5">
                            <template x-for="item in options" :key="item.value">
                                <button type="button"
                                        @click="select(item.value, item.label)"
                                        data-slot="combobox-item"
                                        class="w-full relative flex items-center justify-between px-3 py-2 text-xs rounded-lg transition-colors cursor-pointer select-none text-left"
                                        :class="selectedValue === item.value 
                                            ? 'bg-neutral-100 dark:bg-neutral-800 text-neutral-900 dark:text-white font-medium' 
                                            : 'text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-800/60'">
                                    <span x-text="item.label"></span>
                                    <svg x-show="selectedValue === item.value"
                                         class="w-3.5 h-3.5 text-neutral-900 dark:text-white shrink-0 pointer-events-none transition-transform duration-200"
                                         fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>

                    @if(request()->filled('status'))
                        <a href="{{ route('quiz.history', $quiz) }}" class="text-xs text-gray-400 hover:text-black dark:hover:text-white transition">
                            Xóa lọc
                        </a>
                    @endif
                </div>

                <a href="{{ route('quiz.show', $quiz) }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-black dark:bg-white text-white dark:text-black text-xs font-semibold hover:opacity-90 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Làm bài mới</span>
                </a>
            </form>
        </div>

        {{-- ===== BẢNG DANH SÁCH LỊCH SỬ LÀM BÀI ===== --}}
        <div class="bg-white dark:bg-[#161b22] rounded-2xl border border-gray-100 dark:border-[#30363d] overflow-hidden shadow-sm">
            @if($attempts->isEmpty())
                <div class="p-12 text-center">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Chưa có lịch sử làm bài nào.</p>
                    <p class="text-xs text-gray-400 mt-1">Hãy bắt đầu thử sức bài quiz này ngay bây giờ!</p>
                    <a href="{{ route('quiz.show', $quiz) }}" class="mt-4 inline-flex px-5 py-2 rounded-xl bg-black text-white text-xs font-semibold">
                        Làm bài ngay
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs sm:text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-[#30363d] bg-gray-50/50 dark:bg-[#0d1117]/50 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-gray-400">
                                <th class="py-3.5 px-6">TÊN BÀI QUIZ</th>
                                <th class="py-3.5 px-6">KHÓA HỌC</th>
                                <th class="py-3.5 px-6">NGÀY LÀM</th>
                                <th class="py-3.5 px-6 text-center">ĐIỂM SỐ</th>
                                <th class="py-3.5 px-6 text-center">TRẠNG THÁI</th>
                                <th class="py-3.5 px-6 text-right">THAO TÁC</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-[#30363d]">
                            @foreach($attempts as $attempt)
                                <tr class="hover:bg-gray-50/80 dark:hover:bg-[#21262d]/50 transition">
                                    {{-- Tên Quiz --}}
                                    <td class="py-4 px-6 font-semibold text-gray-900 dark:text-white">
                                        {{ $quiz->title }}
                                    </td>

                                    {{-- Khóa học --}}
                                    <td class="py-4 px-6 text-gray-500 dark:text-gray-400">
                                        {{ $quiz->course->title }}
                                    </td>

                                    {{-- Ngày làm --}}
                                    <td class="py-4 px-6 text-gray-500 dark:text-gray-400">
                                        {{ $attempt->created_at->format('d Thg m, Y H:i') }}
                                    </td>

                                    {{-- Điểm số --}}
                                    <td class="py-4 px-6 text-center font-bold text-gray-900 dark:text-white">
                                        {{ $attempt->score }}%
                                        <span class="block text-[11px] font-normal text-gray-400">({{ $attempt->total_correct }}/{{ $attempt->total_questions }} câu)</span>
                                    </td>

                                    {{-- Trạng thái --}}
                                    <td class="py-4 px-6 text-center">
                                        @if($attempt->is_passed)
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                                HOÀN THÀNH
                                            </span>
                                        @else
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                                CHƯA ĐẠT
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Thao tác --}}
                                    <td class="py-4 px-6 text-right font-semibold">
                                        <a href="{{ route('quiz.result', [$quiz, $attempt]) }}"
                                           class="inline-flex items-center gap-1 text-xs font-bold text-gray-900 dark:text-white hover:underline">
                                            <span>Chi tiết</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($attempts->hasPages())
                    <div class="p-4 border-t border-gray-100 dark:border-[#30363d] flex justify-center">
                        {{ $attempts->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>
</div>
@endsection
