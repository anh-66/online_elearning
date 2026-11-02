{{-- Component: Uiverse Course Card by nehal_6967 (Tailored for EduLearn & BE/DB Data) --}}
@php
    $rating = (float) ($course->reviews_avg_rating ?? $course->reviews->avg('rating') ?? 0);
    $reviewsCount = $course->reviews_count ?? $course->reviews->count();
@endphp

<div class="w-full rounded-2xl border border-gray-200 dark:border-[#30363d] bg-white dark:bg-[#161b22] shadow-sm overflow-hidden group flex flex-col justify-between transition-all duration-200 hover:shadow-md hover:border-[#019D91]/40 dark:hover:border-[#019D91]/40">
    {{-- Thumbnail Media Section --}}
    <a href="{{ route('courses.show', $course->slug) }}"
       class="h-44 w-full flex items-center justify-center bg-gray-100 dark:bg-[#0d1117] text-gray-400 overflow-hidden relative block">
        @if($course->thumbnail)
            <img src="{{ asset('storage/' . $course->thumbnail) }}"
                 alt="{{ $course->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
        @else
            <div class="flex flex-col items-center justify-center gap-2 p-4 text-center">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-10 w-10 text-gray-400 dark:text-[#8b949e]"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 7.5A4.5 4.5 0 017.5 3h9A4.5 4.5 0 0121 7.5v9a4.5 4.5 0 01-4.5 4.5h-9A4.5 4.5 0 013 16.5v-9z"
                    ></path>
                </svg>
                @if($course->category)
                    <span class="text-xs font-medium text-gray-500 dark:text-[#8b949e]">{{ $course->category->name }}</span>
                @endif
            </div>
        @endif
    </a>

    {{-- Main Content Section --}}
    <div class="p-4 flex-1 flex flex-col justify-between">
        <div>
            {{-- Title, Subtitle and Top Badges --}}
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <a href="{{ route('courses.show', $course->slug) }}"
                       class="text-sm font-semibold text-gray-900 dark:text-[#f0f6fc] truncate block hover:text-[#019D91] dark:hover:text-[#02b3a5] transition"
                       title="{{ $course->title }}">
                        {{ $course->title }}
                    </a>
                    <p class="mt-1 text-xs text-gray-500 dark:text-[#8b949e] truncate">
                        {{ $course->instructor->name ?? 'Giảng viên EduLearn' }}
                    </p>
                </div>

                {{-- Badges: Level & Status/Price --}}
                <div class="flex-shrink-0 ml-2 flex items-center gap-1">
                    <span class="text-xs px-2 py-0.5 rounded-full border border-gray-200 dark:border-[#30363d] bg-gray-50 dark:bg-[#21262d] text-gray-600 dark:text-[#c9d1d9]">
                        @if($course->level === 'beginner') Cơ bản
                        @elseif($course->level === 'intermediate') Trung cấp
                        @else Nâng cao @endif
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded-full border border-[#019D91]/20 dark:border-[#019D91]/30 bg-[#019D91]/10 text-[#018076] dark:text-[#02b3a5] font-semibold">
                        @if($course->price == 0)
                            Miễn phí
                        @else
                            {{ number_format($course->price, 0, ',', '.') }} ₫
                        @endif
                    </span>
                </div>
            </div>

            {{-- Additional Info / Meta Text --}}
            <div class="mt-3 text-xs text-gray-500 dark:text-[#8b949e] flex items-center justify-between">
                <div class="flex items-center space-x-1.5">
                    <span class="font-semibold text-gray-900 dark:text-[#f0f6fc]">{{ number_format($rating, 1) }}</span>
                    <span class="text-amber-400">★</span>
                    <span>({{ $reviewsCount }})</span>
                </div>
                <span>{{ number_format($course->enrollments_count ?? 0) }} học viên</span>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-4 flex items-center gap-2">
            <a href="{{ route('courses.show', $course->slug) }}"
               class="flex-1 rounded-xl border border-transparent px-3 py-2 text-xs font-semibold bg-[#019D91] text-white hover:bg-[#008b80] focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-[#019D91]/40 text-center transition shadow-sm active:scale-[0.98]">
                Xem khóa học
            </a>
            <button type="button"
                    onclick="if(navigator.share) { navigator.share({title: '{{ addslashes($course->title) }}', url: '{{ route('courses.show', $course->slug) }}'}); } else { navigator.clipboard.writeText('{{ route('courses.show', $course->slug) }}'); alert('Đã sao chép liên kết khóa học!'); }"
                    class="rounded-xl border border-gray-200 dark:border-[#30363d] px-3 py-2 text-xs font-medium text-gray-700 dark:text-[#c9d1d9] bg-white dark:bg-[#21262d] hover:bg-gray-50 dark:hover:bg-[#30363d] focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-gray-300 transition active:scale-[0.98]">
                Chia sẻ
            </button>
        </div>
    </div>

    {{-- Bottom Tagline / Category Footer --}}
    <div class="px-4 py-2 text-xs text-gray-400 dark:text-[#7d8590] border-t border-gray-100 dark:border-[#30363d] text-center truncate">
        {{ $course->category->name ?? 'Công nghệ' }} • {{ $course->level === 'beginner' ? 'Cơ bản' : ($course->level === 'intermediate' ? 'Trung cấp' : 'Nâng cao') }} • EduLearn
    </div>
</div>
