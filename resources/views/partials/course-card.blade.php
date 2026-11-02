{{-- Partial: Card hiển thị một khóa học --}}
<a href="{{ route('courses.show', $course->slug) }}"
   class="group flex flex-col bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-800 rounded-xl overflow-hidden shadow-sm hover:-translate-y-1 hover:shadow-lg hover:border-brand-200 dark:hover:border-brand-500/40 transition">

    {{-- Thumbnail --}}
    <div class="relative overflow-hidden bg-gray-100 dark:bg-neutral-800 aspect-video">
        @if($course->thumbnail)
            <img src="{{ asset('storage/' . $course->thumbnail) }}"
                 alt="{{ $course->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
        @else
            {{-- Chưa có ảnh: nền gradient theo danh mục để các card không giống hệt nhau --}}
            @php
                $gradients = ['from-emerald-500 to-teal-700', 'from-sky-500 to-indigo-600', 'from-amber-400 to-orange-600', 'from-pink-500 to-rose-600', 'from-violet-500 to-purple-700', 'from-cyan-500 to-blue-600'];
                $gradient = $gradients[($course->category_id ?? $course->id) % count($gradients)];
            @endphp
            <div class="w-full h-full flex flex-col items-center justify-center gap-2 bg-gradient-to-br {{ $gradient }} text-white">
                <svg class="w-10 h-10 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
                @if($course->category)
                    <span class="px-4 text-center text-sm font-semibold">{{ $course->category->name }}</span>
                @endif
            </div>
        @endif

        {{-- Badge trình độ --}}
        <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 text-xs font-semibold rounded-full ring-1 ring-white/60
            @if($course->level === 'beginner') bg-white/95 dark:bg-neutral-900/90 text-emerald-700 dark:text-emerald-400
            @elseif($course->level === 'intermediate') bg-white/95 dark:bg-neutral-900/90 text-amber-700 dark:text-amber-400
            @else bg-white/95 dark:bg-neutral-900/90 text-rose-700 dark:text-rose-400 @endif">
            @if($course->level === 'beginner') Cơ bản
            @elseif($course->level === 'intermediate') Trung cấp
            @else Nâng cao @endif
        </span>
    </div>

    {{-- Content --}}
    <div class="flex flex-col flex-1 p-4">
        {{-- Category --}}
        @if($course->category)
        <p class="text-xs font-semibold uppercase tracking-wide text-brand-700 dark:text-brand-400 mb-1">{{ $course->category->name }}</p>
        @endif

        {{-- Title --}}
        <h3 class="font-semibold text-gray-800 dark:text-neutral-100 text-sm leading-snug mb-2 line-clamp-2 group-hover:text-brand-800 dark:group-hover:text-brand-300 transition">
            {{ $course->title }}
        </h3>

        {{-- Instructor --}}
        <p class="text-xs text-gray-500 dark:text-neutral-400 mb-3">{{ $course->instructor->name ?? 'N/A' }}</p>

        {{-- Rating --}}
        {{-- Dùng điểm tính sẵn (withAvg) hoặc reviews đã eager-load để tránh N+1 query --}}
        @php $rating = (float) ($course->reviews_avg_rating ?? $course->reviews->avg('rating') ?? 0); @endphp
        <div class="flex items-center space-x-1 mb-3">
            @for($i = 1; $i <= 5; $i++)
                <svg class="w-3.5 h-3.5 {{ $i <= round($rating) ? 'text-amber-400' : 'text-gray-200 dark:text-neutral-700' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @endfor
            <span class="text-xs text-gray-500 dark:text-neutral-400 ml-1">
                {{ number_format($rating, 1) }}
                ({{ $course->reviews->count() }})
            </span>
        </div>

        {{-- Footer: price + enrollments --}}
        <div class="mt-auto flex items-center justify-between">
            <span class="font-bold text-brand-700 dark:text-brand-400">
                @if($course->price == 0)
                    <span class="text-emerald-600 dark:text-emerald-400">Miễn phí</span>
                @else
                    {{ number_format($course->price, 0, ',', '.') }} ₫
                @endif
            </span>
            <span class="text-xs text-gray-400 dark:text-neutral-500">
                {{ number_format($course->enrollments_count ?? 0) }} học viên
            </span>
        </div>
    </div>
</a>
