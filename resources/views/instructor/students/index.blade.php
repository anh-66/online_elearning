@extends('layouts.instructor')

@section('title', 'Quản lý học viên')
@section('page_title', 'Học viên theo khóa học')

@section('content')
<div class="space-y-6" x-data="{ activeStudent: null, details: {{ Js::from($studentDetails) }} }">
    {{-- Form lọc khóa học và tìm kiếm --}}
    <form method="GET" action="{{ route('instructor.students.index') }}" class="bg-white dark:bg-neutral-900 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 flex flex-col md:flex-row gap-3">
        <select name="course_id" class="md:w-80 rounded-xl border-gray-300 dark:border-neutral-700 text-sm focus:border-brand-600 focus:ring-brand-600">
            <option value="">Tất cả khóa học</option>
            @foreach($courses as $courseOption)
                <option value="{{ $courseOption->id }}" @selected((string) request('course_id') === (string) $courseOption->id)>{{ $courseOption->title }}</option>
            @endforeach
        </select>
        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm theo tên hoặc email học viên..."
               class="flex-1 rounded-xl border-gray-300 dark:border-neutral-700 text-sm focus:border-brand-600 focus:ring-brand-600">
        <button type="submit" class="px-5 py-2 rounded-xl bg-[#019D91] text-white text-sm font-semibold hover:bg-[#008b80] transition shadow-sm">Lọc</button>
        @if(request()->hasAny(['course_id', 'keyword']))
            <a href="{{ route('instructor.students.index') }}" class="px-5 py-2 rounded-xl bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-200 text-sm font-semibold hover:bg-gray-200 dark:hover:bg-neutral-800 text-center transition">Xóa lọc</a>
        @endif
    </form>

    {{-- Bảng danh sách học viên --}}
    <div class="bg-white dark:bg-neutral-900 rounded-2xl shadow-sm border border-gray-100 dark:border-neutral-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-neutral-800 flex items-center justify-between">
            <h3 class="text-base font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">
                {{ $selectedCourse ? 'Học viên của khóa: '.$selectedCourse->title : 'Tất cả học viên' }}
                <span class="text-sm font-normal text-gray-500 dark:text-neutral-400 normal-case">({{ $enrollments->total() }} lượt ghi danh)</span>
            </h3>
            <span class="text-xs text-gray-400 dark:text-neutral-500 hidden sm:inline">Nhấp vào học viên để xem chi tiết bài học đã hoàn thành</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-neutral-800/40 text-xs uppercase text-gray-500 dark:text-neutral-400">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">Học viên</th>
                        <th class="px-4 py-3 text-left font-semibold">Khóa học</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Ngày ghi danh</th>
                        <th class="px-4 py-3 text-left font-semibold whitespace-nowrap">Hình thức</th>
                        <th class="px-6 py-3 text-left font-semibold w-64">Tiến độ</th>
                        <th class="px-4 py-3 text-center font-semibold whitespace-nowrap">Chi tiết</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                    @forelse($enrollments as $enrollment)
                        @php $studentProgress = $progress[$enrollment->id] ?? ['completed' => 0, 'total' => 0, 'percent' => 0]; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800 transition">
                            <td class="px-6 py-3">
                                <button type="button" @click="activeStudent = details[{{ $enrollment->id }}]" class="flex items-center gap-3 text-left group focus:outline-none">
                                    <div class="w-9 h-9 rounded-full bg-black text-white dark:bg-black dark:text-white font-bold text-sm flex items-center justify-center shrink-0 overflow-hidden ring-2 ring-transparent group-hover:ring-[#019D91] transition">
                                        @if($enrollment->user->avatar_url)
                                            <img src="{{ $enrollment->user->avatar_url }}" referrerpolicy="no-referrer" alt="{{ $enrollment->user->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')">
                                            <span class="hidden flex items-center justify-center w-full h-full">{{ $enrollment->user->initialLetter() }}</span>
                                        @else
                                            <span>{{ $enrollment->user->initialLetter() }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 dark:text-neutral-100 group-hover:text-[#019D91] transition truncate">{{ $enrollment->user->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-neutral-400 truncate">{{ $enrollment->user->email }}</p>
                                    </div>
                                </button>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('instructor.courses.show', $enrollment->course) }}" class="text-gray-700 dark:text-neutral-200 hover:text-[#019D91] dark:hover:text-[#02b3a5] line-clamp-2">{{ $enrollment->course->title }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-neutral-300 whitespace-nowrap">{{ $enrollment->enrolled_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($enrollment->status === \App\Models\Enrollment::STATUS_PAID)
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 whitespace-nowrap">Trả phí</span>
                                @else
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-neutral-800 text-gray-600 dark:text-neutral-300 whitespace-nowrap">Miễn phí</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-2 rounded-full bg-gray-100 dark:bg-neutral-800 overflow-hidden">
                                        <div class="h-full rounded-full {{ $studentProgress['percent'] >= 100 ? 'bg-emerald-500' : 'bg-[#019D91]' }}" style="width: {{ $studentProgress['percent'] }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-700 dark:text-neutral-200 w-10 text-right whitespace-nowrap">{{ $studentProgress['percent'] }}%</span>
                                </div>
                                <p class="text-[11px] text-gray-400 dark:text-neutral-500 mt-1 whitespace-nowrap">{{ $studentProgress['completed'] }}/{{ $studentProgress['total'] }} bài học</p>
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <button type="button" @click="activeStudent = details[{{ $enrollment->id }}]"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-200 hover:bg-[#019D91] hover:text-white dark:hover:bg-[#019D91] dark:hover:text-white transition shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>Xem tiến độ</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-neutral-400">Chưa có học viên nào ghi danh.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $enrollments->links() }}</div>

    {{-- Modal Chi tiết Tiến độ học tập của học viên --}}
    <div x-show="activeStudent" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            {{-- Backdrop --}}
            <div x-show="activeStudent"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="activeStudent = null"
                 class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

            {{-- Modal Content Card --}}
            <div x-show="activeStudent"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative bg-white dark:bg-neutral-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full border border-gray-100 dark:border-neutral-800">
                
                {{-- Modal Header --}}
                <div class="p-6 border-b border-gray-100 dark:border-neutral-800 flex items-start justify-between bg-gray-50/50 dark:bg-neutral-800/30">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#019D91]/15 text-[#018076] dark:bg-[#019D91]/20 dark:text-[#02b3a5]">Chi tiết tiến độ học</span>
                            <span class="text-xs text-gray-400 dark:text-neutral-500" x-text="'Ghi danh: ' + activeStudent?.enrolled_at"></span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-neutral-100" x-text="activeStudent?.student_name"></h3>
                        <p class="text-xs text-gray-500 dark:text-neutral-400" x-text="activeStudent?.student_email + ' · ' + activeStudent?.course_title"></p>
                    </div>
                    <button type="button" @click="activeStudent = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-neutral-200 p-1 rounded-xl hover:bg-gray-100 dark:hover:bg-neutral-800 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Tổng quan tiến độ % --}}
                <div class="p-6 border-b border-gray-100 dark:border-neutral-800 bg-white dark:bg-neutral-900">
                    <div class="flex items-center justify-between text-xs mb-2">
                        <span class="font-semibold text-gray-700 dark:text-neutral-300">Tổng tiến độ hoàn thành</span>
                        <span class="font-bold text-[#019D91] dark:text-[#02b3a5]" x-text="activeStudent?.completed + '/' + activeStudent?.total + ' bài học (' + activeStudent?.percent + '%)'"></span>
                    </div>
                    <div class="w-full h-3 rounded-full bg-gray-100 dark:bg-neutral-800 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-300"
                             :class="activeStudent?.percent >= 100 ? 'bg-emerald-500' : 'bg-[#019D91]'"
                             :style="'width: ' + activeStudent?.percent + '%'"></div>
                    </div>
                </div>

                {{-- Danh sách bài học theo chương --}}
                <div class="p-6 max-h-[60vh] overflow-y-auto space-y-6">
                    <template x-for="(chapter, cIdx) in activeStudent?.chapters" :key="cIdx">
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold text-gray-500 dark:text-neutral-400 uppercase tracking-wider flex items-center justify-between"
                                x-text="'Chương ' + (cIdx + 1) + ': ' + chapter.title"></h4>
                            
                            <div class="divide-y divide-gray-100 dark:divide-neutral-800 rounded-2xl border border-gray-100 dark:border-neutral-800 overflow-hidden">
                                <template x-for="(lesson, lIdx) in chapter.lessons" :key="lIdx">
                                    <div class="p-3.5 flex items-center justify-between gap-3 text-xs bg-white dark:bg-neutral-900 hover:bg-gray-50/50 dark:hover:bg-neutral-800/40 transition">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="w-5 h-5 rounded-full flex items-center justify-center shrink-0"
                                                  :class="lesson.is_completed ? 'bg-emerald-500 text-white' : 'bg-gray-100 dark:bg-neutral-800 text-gray-400 dark:text-neutral-500'">
                                                <template x-if="lesson.is_completed">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                </template>
                                                <template x-if="!lesson.is_completed">
                                                    <span class="text-[10px]" x-text="lIdx + 1"></span>
                                                </template>
                                            </span>
                                            <span class="font-medium text-gray-900 dark:text-neutral-100 truncate" x-text="lesson.title"></span>
                                        </div>

                                        <div class="shrink-0 text-right">
                                            <template x-if="lesson.is_completed">
                                                <span class="inline-flex items-center gap-1 text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">
                                                    <span>Đã hoàn thành</span>
                                                    <span class="text-[10px] text-gray-400 dark:text-neutral-500" x-show="lesson.completed_at" x-text="'(' + lesson.completed_at + ')'"></span>
                                                </span>
                                            </template>
                                            <template x-if="!lesson.is_completed">
                                                <span class="text-[11px] text-gray-400 dark:text-neutral-500">Chưa hoàn thành</span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Phần Quiz trắc nghiệm nếu có --}}
                    <template x-if="activeStudent?.quizzes && activeStudent.quizzes.length > 0">
                        <div class="space-y-3 pt-4 border-t border-gray-100 dark:border-neutral-800">
                            <h4 class="text-xs font-bold text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Kết quả Quiz Trắc nghiệm</h4>
                            <div class="divide-y divide-gray-100 dark:divide-neutral-800 rounded-2xl border border-gray-100 dark:border-neutral-800 overflow-hidden">
                                <template x-for="(quiz, qIdx) in activeStudent.quizzes" :key="qIdx">
                                    <div class="p-3.5 flex items-center justify-between gap-3 text-xs bg-white dark:bg-neutral-900">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="w-5 h-5 rounded-lg bg-[#019D91]/15 text-[#018076] dark:text-[#02b3a5] font-bold text-[10px] flex items-center justify-center shrink-0">Q</span>
                                            <span class="font-medium text-gray-900 dark:text-neutral-100 truncate" x-text="quiz.title"></span>
                                        </div>
                                        <div class="shrink-0">
                                            <template x-if="quiz.has_attempted">
                                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold"
                                                      :class="quiz.is_passed ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400'"
                                                      x-text="(quiz.is_passed ? 'Đạt' : 'Chưa đạt') + ' (' + quiz.score + '%)'"></span>
                                            </template>
                                            <template x-if="!quiz.has_attempted">
                                                <span class="text-[11px] text-gray-400 dark:text-neutral-500">Chưa làm bài</span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Modal Footer --}}
                <div class="p-4 bg-gray-50/50 dark:bg-neutral-800/30 border-t border-gray-100 dark:border-neutral-800 flex justify-end">
                    <button type="button" @click="activeStudent = null" class="px-5 py-2 text-xs font-semibold rounded-xl bg-gray-200 dark:bg-neutral-700 text-gray-700 dark:text-neutral-200 hover:bg-gray-300 dark:hover:bg-neutral-600 transition">
                        Đóng
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
