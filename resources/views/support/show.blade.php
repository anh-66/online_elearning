@extends('layouts.public')

@section('title', $article['title'] . ' - Hỗ Trợ EduLearn')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        .font-inter,
        .font-inter * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }
        .font-inter {
            font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11';
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
@endpush

@section('content')
<div class="min-h-screen bg-[#fcfcfc] dark:bg-[#0d1117] text-[#111827] dark:text-neutral-100 transition-colors pb-24 font-inter"
     x-data="{ feedbackGiven: false, feedbackHelpful: true }">

    {{-- Subtle Breadcrumb (Tối giản chuẩn Course Show) --}}
    <div class="max-w-7xl mx-auto px-6 md:px-12 pt-6 pb-2 flex items-center justify-between text-xs sm:text-sm text-gray-400 dark:text-neutral-500">
        <div class="flex items-center gap-2 truncate">
            <a href="{{ route('home') }}" class="hover:text-black dark:hover:text-white transition-colors">Trang chủ</a>
            <span>&rsaquo;</span>
            <a href="{{ route('support.index') }}" class="hover:text-black dark:hover:text-white transition-colors">Hỗ trợ</a>
            <span>&rsaquo;</span>
            <a href="{{ route('support.index', ['category' => $article['category_slug']]) }}" class="hover:text-black dark:hover:text-white transition-colors truncate">{{ $article['category'] }}</a>
            <span>&rsaquo;</span>
            <span class="text-gray-900 dark:text-white font-medium truncate">{{ $article['badge'] }}</span>
        </div>

        <a href="{{ route('support.index') }}" class="hover:text-black dark:hover:text-white transition-colors shrink-0 ml-4 font-medium">
            ← Tất cả chủ đề
        </a>
    </div>

    {{-- Main Content Wrapper (Layout thoáng đãng, tinh gọn, không chia khối hộp) --}}
    <main class="max-w-7xl mx-auto px-6 md:px-12 pt-6 md:pt-10">
        <div class="flex flex-col lg:flex-row gap-12 lg:gap-16">

            {{-- Cột trái: Nội dung hướng dẫn --}}
            <article class="flex-1 order-2 lg:order-1 min-w-0 space-y-8">

                {{-- Tiêu đề & Danh mục --}}
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-2.5 py-1 bg-gray-100 dark:bg-neutral-800 text-[11px] font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 rounded">
                            {{ $article['badge'] }}
                        </span>
                        <span class="text-xs text-gray-400 dark:text-neutral-500">•</span>
                        <span class="text-xs text-gray-500 dark:text-neutral-400 font-medium">{{ $article['category'] }}</span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight leading-[1.15] mb-5 text-gray-900 dark:text-white">
                        {{ $article['title'] }}
                    </h1>

                    <p class="text-base sm:text-lg text-gray-500 dark:text-neutral-400 leading-relaxed max-w-3xl">
                        {{ $article['summary'] }}
                    </p>
                </div>

                {{-- Lời mở đầu & Thao tác nhanh (Thiết kế phẳng tối giản, viền line mỏng, không nền hộp) --}}
                <div class="py-3 border-l-2 border-[#019D91] pl-4 space-y-2">
                    <p class="text-sm sm:text-base text-gray-600 dark:text-neutral-300 leading-relaxed">
                        {{ $article['intro'] }}
                    </p>
                    @if(!empty($article['action_label']) && !empty($article['action_url']))
                        <div class="flex items-center gap-2 pt-1 text-xs sm:text-sm">
                            <span class="text-gray-400 dark:text-neutral-500">Thao tác nhanh:</span>
                            <a href="{{ $article['action_url'] }}"
                               class="font-semibold text-[#019D91] dark:text-[#02b3a5] hover:underline inline-flex items-center gap-1">
                                <span>{{ $article['action_label'] }}</span>
                                <span>→</span>
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Các bước thao tác (Dạng timeline phẳng, loại bỏ khối hộp rườm rà) --}}
                <div class="pt-4">
                    <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 dark:text-white mb-6">
                        {{ $article['steps_heading'] }}
                    </h2>

                    <div class="space-y-6">
                        @foreach($article['steps'] as $step)
                            <div class="flex items-start gap-4">
                                <div class="w-8 h-8 rounded-full bg-gray-900 text-white dark:bg-white dark:text-black font-bold flex items-center justify-center shrink-0 text-xs mt-0.5 shadow-xs">
                                    {{ $step['step_number'] }}
                                </div>
                                <div class="flex-1 pb-6 border-b border-gray-100 dark:border-neutral-800 space-y-1.5">
                                    <h3 class="font-semibold text-gray-900 dark:text-white text-base">
                                        {{ $step['title'] }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-neutral-400 leading-relaxed">
                                        {{ $step['description'] }}
                                    </p>
                                    @if(!empty($step['link_label']) && !empty($step['link_url']))
                                        <div class="pt-1">
                                            <a href="{{ $step['link_url'] }}"
                                               class="inline-flex items-center gap-1 font-semibold text-xs text-[#019D91] dark:text-[#02b3a5] hover:underline">
                                                <span>{{ $step['link_label'] }}</span>
                                                <span>→</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Lưu ý quan trọng (nếu có) --}}
                @if(!empty($article['notes']))
                    <div class="p-5 rounded-2xl bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-800/40 text-sm">
                        <h4 class="text-xs font-bold text-amber-800 dark:text-amber-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Lưu ý quan trọng
                        </h4>
                        <ul class="list-disc list-inside space-y-1.5 text-xs sm:text-sm text-amber-900/80 dark:text-amber-300/80">
                            @foreach($article['notes'] as $note)
                                <li>{{ $note }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Đánh giá bài viết --}}
                <div class="py-6 border-t border-gray-100 dark:border-neutral-800">
                    <template x-if="!feedbackGiven">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <p class="text-sm font-medium text-gray-700 dark:text-neutral-300">
                                Bài viết này có hữu ích với bạn không?
                            </p>
                            <div class="inline-flex items-center gap-2">
                                <button type="button"
                                        @click="feedbackGiven = true; feedbackHelpful = true"
                                        class="px-4 py-1.5 rounded-lg border border-gray-200 dark:border-neutral-700 text-xs font-medium text-gray-700 dark:text-neutral-300 hover:bg-gray-50 dark:hover:bg-neutral-800 transition active:scale-95">
                                    Có, rất hữu ích
                                </button>
                                <button type="button"
                                        @click="feedbackGiven = true; feedbackHelpful = false"
                                        class="px-4 py-1.5 rounded-lg border border-gray-200 dark:border-neutral-700 text-xs font-medium text-gray-700 dark:text-neutral-300 hover:bg-gray-50 dark:hover:bg-neutral-800 transition active:scale-95">
                                    Không
                                </button>
                            </div>
                        </div>
                    </template>
                    <template x-if="feedbackGiven">
                        <div class="text-xs font-medium text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Cảm ơn bạn đã đóng góp phản hồi!</span>
                        </div>
                    </template>
                </div>

            </article>

            {{-- Cột phải: Sidebar thẻ đơn tinh gọn chuẩn Course Show --}}
            <aside class="w-full lg:w-80 xl:w-96 order-1 lg:order-2 shrink-0">
                <div class="sticky top-28 bg-white dark:bg-[#161b22] border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 shadow-sm space-y-6">

                    {{-- Hành động nhanh --}}
                    <div class="space-y-3">
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">
                            Sẵn sàng thực hiện ngay?
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-neutral-400 leading-relaxed">
                            {{ $article['action_help'] ?? 'Bấm nút để truy cập thẳng vào trang xử lý tính năng trong tài khoản của bạn.' }}
                        </p>
                        
                        <div class="pt-2 space-y-2">
                            <a href="{{ $article['action_url'] }}"
                               class="w-full py-3 bg-black text-white dark:bg-white dark:text-black font-semibold text-sm rounded-xl hover:bg-neutral-800 dark:hover:bg-neutral-200 transition text-center block shadow-xs">
                                {{ $article['action_label'] }}
                            </a>
                            <a href="{{ route('support.index') }}"
                               class="w-full py-2.5 bg-gray-100 dark:bg-neutral-800 text-gray-800 dark:text-neutral-200 font-semibold text-xs rounded-xl hover:bg-gray-200 dark:hover:bg-neutral-700 transition text-center block">
                                Tìm kiếm chủ đề khác
                            </a>
                        </div>
                    </div>

                    {{-- Chủ đề liên quan (Dạng text link tối giản, không lồng hộp) --}}
                    @if(!empty($relatedArticles))
                        <div class="pt-5 border-t border-gray-100 dark:border-neutral-800 space-y-3">
                            <span class="text-[11px] font-bold text-gray-400 dark:text-neutral-500 uppercase tracking-wider block">
                                Chủ đề liên quan
                            </span>
                            <div class="space-y-3">
                                @foreach($relatedArticles as $related)
                                    <a href="{{ route('support.show', $related['slug']) }}"
                                       class="group block pb-2 border-b border-gray-50 dark:border-neutral-800/60 last:border-0 last:pb-0">
                                        <p class="text-[10px] font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider">{{ $related['category'] }}</p>
                                        <p class="text-sm font-medium text-gray-800 dark:text-neutral-200 group-hover:text-[#019D91] transition mt-0.5 leading-snug">
                                            {{ $related['title'] }}
                                        </p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Trợ giúp bổ sung --}}
                    <div class="pt-4 border-t border-gray-100 dark:border-neutral-800 text-xs text-gray-500 dark:text-neutral-400 flex items-center justify-between">
                        <span>Vẫn cần hỗ trợ?</span>
                        <a href="{{ route('support.feedback') }}" class="font-semibold text-[#019D91] dark:text-[#02b3a5] hover:underline">
                            Gửi phản hồi →
                        </a>
                    </div>

                </div>
            </aside>

        </div>
    </main>

</div>
@endsection
