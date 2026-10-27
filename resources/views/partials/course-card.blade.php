{{-- Partial: Card hiển thị một khóa học --}}
<a href="{{ route('courses.show', $course->slug) }}"
   class="group flex flex-col bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md hover:border-gray-300 transition">

    {{-- Thumbnail --}}
    <div class="relative overflow-hidden bg-gray-100 aspect-video">
        @if($course->thumbnail)
            <img src="{{ asset('storage/' . $course->thumbnail) }}"
                 alt="{{ $course->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center bg-indigo-50">
                <svg class="w-12 h-12 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        @endif

        {{-- Badge trình độ --}}
        <span class="absolute top-2 left-2 px-2 py-0.5 text-xs font-medium rounded-md
            @if($course->level === 'beginner') bg-emerald-100 text-emerald-700
            @elseif($course->level === 'intermediate') bg-amber-100 text-amber-700
            @else bg-rose-100 text-rose-700 @endif">
            @if($course->level === 'beginner') Cơ bản
            @elseif($course->level === 'intermediate') Trung cấp
            @else Nâng cao @endif
        </span>
    </div>

    {{-- Content --}}
    <div class="flex flex-col flex-1 p-4">
        {{-- Category --}}
        @if($course->category)
        <p class="text-xs font-medium text-indigo-600 mb-1">{{ $course->category->name }}</p>
        @endif

        {{-- Title --}}
        <h3 class="font-semibold text-gray-800 text-sm leading-snug mb-2 line-clamp-2 group-hover:text-indigo-700 transition">
            {{ $course->title }}
        </h3>

        {{-- Instructor --}}
        <p class="text-xs text-gray-500 mb-3">{{ $course->instructor->name ?? 'N/A' }}</p>

        {{-- Rating --}}
        @php $rating = $course->averageRating(); @endphp
        <div class="flex items-center space-x-1 mb-3">
            @for($i = 1; $i <= 5; $i++)
                <svg class="w-3.5 h-3.5 {{ $i <= round($rating) ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @endfor
            <span class="text-xs text-gray-500 ml-1">
                {{ number_format($rating, 1) }}
                ({{ $course->reviews->count() }})
            </span>
        </div>

        {{-- Footer: price + enrollments --}}
        <div class="mt-auto flex items-center justify-between">
            <span class="font-bold text-gray-900">
                @if($course->price == 0)
                    <span class="text-emerald-600">Miễn phí</span>
                @else
                    {{ number_format($course->price, 0, ',', '.') }} ₫
                @endif
            </span>
            <span class="text-xs text-gray-400">
                {{ number_format($course->enrollments_count ?? 0) }} học viên
            </span>
        </div>
    </div>
</a>
