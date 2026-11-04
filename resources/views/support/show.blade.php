@extends('layouts.public')

@section('title', $article['title'] . ' - Hỗ Trợ EduLearn')

@section('content')
<div class="min-h-screen bg-white text-[#1D1D1F] dark:bg-[#000000] dark:text-[#f5f5f7] transition-colors pb-24"
     x-data="{ feedbackGiven: false, feedbackHelpful: true }">

    <!-- Sub-navigation / Breadcrumbs -->
    <div class="border-b border-[#E5E5EA] dark:border-[#222224] bg-[#F5F5F7]/80 dark:bg-[#161617]/80 backdrop-blur-md sticky top-16 z-30">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between text-xs sm:text-sm">
            <nav class="flex items-center space-x-2 text-[#86868B] dark:text-[#a1a1a6] truncate">
                <a href="{{ route('support.index') }}" class="hover:text-[#1D1D1F] dark:hover:text-white transition">Hỗ Trợ</a>
                <span>/</span>
                <a href="{{ route('support.index', ['category' => $article['category_slug']]) }}" class="hover:text-[#1D1D1F] dark:hover:text-white transition truncate">{{ $article['category'] }}</a>
                <span>/</span>
                <span class="text-[#1D1D1F] dark:text-white font-medium truncate">{{ $article['badge'] }}</span>
            </nav>

            <a href="{{ route('support.index') }}" class="text-[#019D91] dark:text-[#02b3a5] hover:underline font-medium shrink-0 ml-4">
                ← Tất cả chủ đề
            </a>
        </div>
    </div>

    <!-- Article Container -->
    <article class="max-w-3xl mx-auto px-4 sm:px-6 pt-10 sm:pt-16">
        <!-- Badge -->
        <div class="mb-4">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#019D91]/15 text-[#018076] dark:bg-[#019D91]/25 dark:text-[#02b3a5]">
                {{ $article['badge'] }}
            </span>
        </div>

        <!-- Article Main Title -->
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-[#1D1D1F] dark:text-white leading-tight">
            {{ $article['title'] }}
        </h1>

        <!-- Subtitle / Summary -->
        <p class="mt-4 text-lg sm:text-xl text-[#6E6E73] dark:text-[#a1a1a6] leading-relaxed">
            {{ $article['summary'] }}
        </p>

        <!-- Intro Paragraph with Inline Links -->
        <div class="mt-6 p-4 sm:p-5 rounded-2xl bg-[#F5F5F7] dark:bg-[#1c1c1e] border border-[#E5E5EA] dark:border-[#2c2c2e] text-sm sm:text-base leading-relaxed text-[#1D1D1F] dark:text-[#e6edf3]">
            <p>
                {{ $article['intro'] }}
            </p>
            <div class="mt-3 pt-3 border-t border-[#E5E5EA] dark:border-[#2c2c2e] flex flex-wrap items-center gap-3">
                <span class="text-xs text-[#86868B] dark:text-[#a1a1a6]">Thao tác nhanh:</span>
                <a href="{{ $article['action_url'] }}"
                   class="font-semibold text-xs sm:text-sm text-[#019D91] dark:text-[#02b3a5] hover:underline inline-flex items-center gap-1">
                    <span>{{ $article['action_label'] }}</span>
                    <span>→</span>
                </a>
            </div>
        </div>

        <!-- Laptop / Computer Illustration Section (Requested by User) -->
        <div class="my-10">
            <div class="text-center mb-2">
                <span class="text-xs font-medium text-[#86868B] dark:text-[#7d8590] uppercase tracking-wider">
                    {{ $article['device_title'] }}
                </span>
            </div>

            @include('support.partials.laptop-mockup', [
                'type' => $article['illustration_type'] ?? 'password_change',
                'title' => $article['device_title'] ?? 'EduLearn Platform'
            ])
        </div>

        <!-- Detailed Step-by-Step Section (Image 3 Style) -->
        <div class="mt-12">
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#1D1D1F] dark:text-white mb-6">
                {{ $article['steps_heading'] }}
            </h2>

            <ol class="space-y-6 text-sm sm:text-base">
                @foreach($article['steps'] as $step)
                    <li class="flex items-start space-x-4">
                        <!-- Step Number Badge -->
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-[#1D1D1F] text-white dark:bg-white dark:text-black font-bold flex items-center justify-center shrink-0 text-xs sm:text-sm mt-0.5 shadow-sm">
                            {{ $step['step_number'] }}
                        </div>

                        <!-- Step Details -->
                        <div class="flex-1 space-y-1">
                            <h3 class="font-bold text-[#1D1D1F] dark:text-white text-base sm:text-lg">
                                {{ $step['title'] }}
                            </h3>
                            <p class="text-[#6E6E73] dark:text-[#a1a1a6] leading-relaxed">
                                {{ $step['description'] }}
                            </p>

                            @if(!empty($step['link_label']) && !empty($step['link_url']))
                                <div class="pt-1">
                                    <a href="{{ $step['link_url'] }}"
                                       class="inline-flex items-center gap-1 font-semibold text-xs sm:text-sm text-[#019D91] dark:text-[#02b3a5] hover:underline">
                                        <span>{{ $step['link_label'] }}</span>
                                        <span>→</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>

        <!-- Primary Action Callout Box -->
        <div class="my-10 p-6 sm:p-8 rounded-3xl bg-[#E8F7F5] dark:bg-[#123331] border border-[#019D91]/30 dark:border-[#019D91]/40 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h4 class="text-base sm:text-lg font-bold text-[#1D1D1F] dark:text-white">
                    Sẵn sàng thực hiện ngay?
                </h4>
                <p class="text-xs sm:text-sm text-[#6E6E73] dark:text-[#a1a1a6] mt-1">
                    {{ $article['action_help'] ?? 'Bấm nút để truy cập thẳng vào trang xử lý chức năng trong tài khoản của bạn.' }}
                </p>
            </div>
            <a href="{{ $article['action_url'] }}"
               class="btn-apple-primary text-xs sm:text-sm px-6 py-3 shrink-0 shadow-md">
                {{ $article['action_label'] }} →
            </a>
        </div>

        <!-- Helpful Notes / Tips -->
        @if(!empty($article['notes']))
            <div class="my-10 p-5 sm:p-6 rounded-2xl bg-[#F5F5F7] dark:bg-[#1c1c1e] border-l-4 border-[#019D91] dark:border-[#02b3a5]">
                <h4 class="text-xs font-semibold text-[#86868B] dark:text-[#7d8590] uppercase tracking-wider mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#019D91] dark:text-[#02b3a5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Lưu ý quan trọng
                </h4>
                <ul class="list-disc list-inside space-y-1.5 text-xs sm:text-sm text-[#6E6E73] dark:text-[#a1a1a6]">
                    @foreach($article['notes'] as $note)
                        <li>{{ $note }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Article Feedback Section -->
        <div class="my-12 py-8 border-t border-b border-[#E5E5EA] dark:border-[#2c2c2e] text-center">
            <template x-if="!feedbackGiven">
                <div>
                    <p class="text-sm sm:text-base font-semibold text-[#1D1D1F] dark:text-white mb-3">
                        Bài viết này có hữu ích với bạn không?
                    </p>
                    <div class="inline-flex items-center gap-3">
                        <button type="button"
                                @click="feedbackGiven = true; feedbackHelpful = true"
                                class="px-5 py-2 rounded-full border border-[#D2D2D7] dark:border-[#3a3a3c] text-xs sm:text-sm font-semibold hover:bg-[#F5F5F7] dark:hover:bg-[#2c2c2e] transition active:scale-95">
                            Có, rất hữu ích
                        </button>
                        <button type="button"
                                @click="feedbackGiven = true; feedbackHelpful = false"
                                class="px-5 py-2 rounded-full border border-[#D2D2D7] dark:border-[#3a3a3c] text-xs sm:text-sm font-semibold hover:bg-[#F5F5F7] dark:hover:bg-[#2c2c2e] transition active:scale-95">
                            Không
                        </button>
                    </div>
                </div>
            </template>
            <template x-if="feedbackGiven">
                <div class="text-sm font-medium text-emerald-600 dark:text-emerald-400 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Cảm ơn bạn đã đóng góp phản hồi giúp hoàn thiện tài liệu hỗ trợ!</span>
                </div>
            </template>
        </div>

        <!-- Related Guides & Topics -->
        @if(!empty($relatedArticles))
            <div class="mt-12">
                <h3 class="text-xl font-bold tracking-tight text-[#1D1D1F] dark:text-white mb-6">
                    Các chủ đề liên quan
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($relatedArticles as $related)
                        <a href="{{ route('support.show', $related['slug']) }}"
                           class="p-4 rounded-2xl bg-[#F5F5F7] dark:bg-[#161617] border border-[#E5E5EA] dark:border-[#2c2c2e] hover:border-[#019D91]/40 transition group flex flex-col justify-between">
                            <div>
                                <span class="text-[10px] font-semibold text-[#86868B] dark:text-[#7d8590] uppercase">{{ $related['category'] }}</span>
                                <h4 class="text-sm font-bold text-[#1D1D1F] dark:text-white group-hover:text-[#019D91] transition mt-1">
                                    {{ $related['title'] }}
                                </h4>
                            </div>
                            <span class="text-xs font-semibold text-[#019D91] dark:text-[#02b3a5] mt-4 flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                                Xem các bước →
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </article>
</div>
@endsection
