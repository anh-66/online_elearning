@extends('layouts.public')

@section('title', 'Trung tâm Hỗ trợ EduLearn')

@section('content')
<div class="min-h-screen bg-[#F5F5F7] dark:bg-[#0d1117] transition-colors pb-20"
     x-data="{
        searchQuery: '{{ addslashes($searchKeyword) }}',
        activeCat: '{{ $activeCategory }}',
        allArticles: {{ Js::from($articles) }},
        quickLinks: {{ Js::from($quickLinks) }},
        activeFaq: null,
        get filteredList() {
            let q = this.searchQuery.trim().toLowerCase();
            return Object.values(this.allArticles).filter(a => {
                let matchesCategory = this.activeCat === 'all' || a.category_slug === this.activeCat;
                if (!matchesCategory) return false;
                if (!q) return true;
                let inTitle = a.title.toLowerCase().includes(q);
                let inSummary = a.summary.toLowerCase().includes(q);
                let inCategory = a.category.toLowerCase().includes(q);
                let inSteps = (a.steps || []).some(s => s.title.toLowerCase().includes(q) || s.description.toLowerCase().includes(q));
                return inTitle || inSummary || inCategory || inSteps;
            });
        }
     }">

    <!-- Top Hero Section -->
    <section class="pt-14 pb-10 sm:pt-20 sm:pb-14 text-center px-4 sm:px-6">
        <div class="max-w-4xl mx-auto">
            <span class="inline-block px-3.5 py-1 text-xs font-semibold uppercase tracking-wider rounded-full bg-[#019D91]/10 text-[#018076] dark:bg-[#019D91]/20 dark:text-[#02b3a5] mb-4">
                Trung Tâm Trợ Giúp EduLearn
            </span>
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-[#1D1D1F] dark:text-white tracking-tight leading-tight">
                Hỗ Trợ EduLearn
            </h1>
            <p class="mt-4 text-base sm:text-xl text-[#86868B] dark:text-[#7d8590] max-w-2xl mx-auto">
                Tìm kiếm giải pháp, hướng dẫn từng bước và liên kết trực tiếp tới các tính năng trong hệ thống.
            </p>
        </div>
    </section>

    <!-- Search Section (Designed exactly like User's Image 2) -->
    <section class="max-w-3xl mx-auto px-4 sm:px-6 mb-16">
        <h2 class="text-2xl sm:text-3xl font-bold text-[#1D1D1F] dark:text-white text-center mb-6 tracking-tight">
            Tìm kiếm các chủ đề khác
        </h2>

        <!-- Search Container -->
        <div class="bg-white dark:bg-[#161b22] rounded-2xl sm:rounded-3xl border-2 transition duration-200 shadow-sm overflow-hidden"
             :class="{ 'border-[#019D91] ring-4 ring-[#019D91]/15': isFocused, 'border-[#E5E5EA] dark:border-[#30363d]': !isFocused }"
             x-data="{ isFocused: false }"
             @click.outside="isFocused = false">

            <!-- Search Input Bar -->
            <div class="relative flex items-center px-4 sm:px-5 py-3 sm:py-3.5 border-b border-[#E5E5EA] dark:border-[#30363d]">
                <svg class="w-5 h-5 text-[#86868B] dark:text-[#7d8590] mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text"
                       x-model="searchQuery"
                       @focus="isFocused = true"
                       @blur="setTimeout(() => isFocused = false, 250)"
                       placeholder="Hỗ trợ tìm kiếm"
                       class="w-full bg-transparent border-none text-[#1D1D1F] dark:text-white placeholder-[#86868B] dark:placeholder-[#7d8590] text-sm sm:text-base focus:ring-0 focus:outline-none p-0"
                       aria-label="Hỗ trợ tìm kiếm">

                <button x-show="searchQuery"
                        x-cloak
                        @click="searchQuery = ''"
                        class="text-[#86868B] hover:text-[#1D1D1F] dark:hover:text-white p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Quick Links / Real-time search dropdown area -->
            <div class="p-4 sm:p-6 text-sm"
                 x-show="isFocused"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1">
                <!-- If typing: show matched search results -->
                <template x-if="searchQuery.trim().length > 0">
                    <div>
                        <div class="text-xs font-semibold text-[#86868B] dark:text-[#7d8590] uppercase tracking-wider mb-3">
                            Kết quả tìm kiếm phù hợp (<span x-text="filteredList.length"></span>)
                        </div>
                        <template x-if="filteredList.length === 0">
                            <div class="py-4 text-center text-[#86868B] dark:text-[#7d8590]">
                                Không tìm thấy chủ đề nào khớp với "<span x-text="searchQuery" class="font-medium"></span>". Thử tìm từ khóa khác như "mật khẩu", "gói", "chứng chỉ".
                            </div>
                        </template>
                        <div class="space-y-2.5">
                            <template x-for="item in filteredList" :key="item.slug">
                                <a :href="'{{ url('/support') }}/' + item.slug"
                                   class="flex items-center justify-between p-2.5 rounded-xl hover:bg-[#F5F5F7] dark:hover:bg-[#21262d] transition group">
                                    <div class="flex items-center space-x-3 truncate">
                                        <div class="w-7 h-7 rounded-lg bg-[#019D91]/10 text-[#019D91] flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div class="truncate">
                                            <span class="font-semibold text-[#1D1D1F] dark:text-white group-hover:text-[#019D91] dark:group-hover:text-[#02b3a5]" x-text="item.title"></span>
                                            <span class="block text-xs text-[#86868B] dark:text-[#7d8590] truncate" x-text="item.summary"></span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-medium text-[#019D91] group-hover:translate-x-1 transition-transform ml-2 shrink-0">Xem hướng dẫn →</span>
                                </a>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- If NOT typing: show default quick links -->
                <template x-if="searchQuery.trim().length === 0">
                    <div>
                        <div class="text-xs font-semibold text-[#86868B] dark:text-[#7d8590] uppercase tracking-wider mb-3.5">
                            Liên kết nhanh
                        </div>
                        <ul class="space-y-2.5">
                            @foreach($quickLinks as $link)
                                <li>
                                    <a href="{{ $link['url'] }}"
                                       class="flex items-center space-x-2 text-[#1D1D1F] dark:text-[#e6edf3] hover:text-[#019D91] dark:hover:text-[#02b3a5] transition group py-1">
                                        <svg class="w-3.5 h-3.5 text-[#86868B] dark:text-[#7d8590] group-hover:text-[#019D91] group-hover:translate-x-0.5 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                        <span class="group-hover:underline font-medium">{{ $link['title'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- Support Categories Section -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 mb-16">
        <div class="text-center mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-[#1D1D1F] dark:text-white tracking-tight">
                Khám phá theo danh mục
            </h2>
            <p class="text-sm sm:text-base text-[#86868B] dark:text-[#7d8590] mt-1">
                Chọn danh mục bạn quan tâm để xem các bài viết giải đáp liên quan
            </p>
        </div>

        <!-- Category Filter Pills -->
        <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3 mb-10">
            @foreach($categories as $category)
                <button type="button"
                        @click="activeCat = '{{ $category['slug'] }}'"
                        :class="activeCat === '{{ $category['slug'] }}'
                            ? 'bg-[#019D91] text-white shadow-sm ring-2 ring-[#019D91]/30 font-semibold'
                            : 'bg-white dark:bg-[#161b22] text-[#1D1D1F] dark:text-[#e6edf3] hover:bg-neutral-100 dark:hover:bg-[#21262d] border border-[#E5E5EA] dark:border-[#30363d] font-medium'"
                        class="px-4 py-2 rounded-full text-xs sm:text-sm transition duration-150 flex items-center gap-1.5 active:scale-95">
                    <span>{{ $category['name'] }}</span>
                </button>
            @endforeach
        </div>

        <!-- Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template x-for="article in filteredList" :key="article.slug">
                <div class="bg-white dark:bg-[#161b22] rounded-3xl p-6 border border-[#E5E5EA] dark:border-[#30363d] shadow-sm hover:shadow-md hover:border-[#019D91]/40 transition duration-300 flex flex-col justify-between group">
                    <div>
                        <!-- Badge & Category -->
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#019D91]/10 text-[#018076] dark:bg-[#019D91]/20 dark:text-[#02b3a5]" x-text="article.badge"></span>
                            <span class="text-xs text-[#86868B] dark:text-[#7d8590]" x-text="article.category"></span>
                        </div>

                        <!-- Title -->
                        <h3 class="text-lg font-bold text-[#1D1D1F] dark:text-white group-hover:text-[#019D91] dark:group-hover:text-[#02b3a5] transition-colors leading-snug" x-text="article.title"></h3>

                        <!-- Summary -->
                        <p class="text-sm text-[#86868B] dark:text-[#7d8590] mt-2 line-clamp-3 leading-relaxed" x-text="article.summary"></p>

                        <!-- Step count indicator -->
                        <div class="mt-4 flex items-center text-xs text-[#86868B] dark:text-[#7d8590] space-x-1">
                            <svg class="w-4 h-4 text-[#019D91]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            <span x-text="(article.steps || []).length + ' bước thao tác rõ ràng'"></span>
                        </div>
                    </div>

                    <!-- Footer Action Links -->
                    <div class="mt-6 pt-4 border-t border-[#E5E5EA] dark:border-[#30363d] flex items-center justify-between text-sm">
                        <a :href="'{{ url('/support') }}/' + article.slug"
                           class="font-semibold text-[#019D91] dark:text-[#02b3a5] hover:underline flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                            <span>Xem hướng dẫn chi tiết</span>
                            <span>→</span>
                        </a>
                        <a :href="article.action_url"
                           class="text-xs text-[#86868B] dark:text-[#7d8590] hover:text-[#1D1D1F] dark:hover:text-white underline">
                            Đi đến tính năng
                        </a>
                    </div>
                </div>
            </template>
        </div>
    </section>

    <!-- Prominent Guides Preview with Laptop Mockup (Image 3 Showcase) -->
    <section class="max-w-5xl mx-auto px-4 sm:px-6 mb-20">
        <div class="bg-[#F5F5F7] dark:bg-[#161b22] rounded-3xl p-6 sm:p-10 border border-[#E5E5EA] dark:border-[#30363d] shadow-lg">
            <div class="text-center max-w-2xl mx-auto mb-8">
                <span class="text-xs font-semibold uppercase tracking-wider text-[#019D91] dark:text-[#02b3a5]">
                    Hướng dẫn thao tác trực quan
                </span>
                <h2 class="text-2xl sm:text-4xl font-bold text-[#1D1D1F] dark:text-white mt-1 tracking-tight">
                    Minh họa các bước thực hiện
                </h2>
                <p class="text-sm sm:text-base text-[#86868B] dark:text-[#7d8590] mt-2">
                    Mỗi hướng dẫn đều đi kèm đường dẫn thực hiện trực tiếp trong hệ thống và giao diện mô phỏng trên máy tính.
                </p>
            </div>

            <!-- Carousel / Featured Guide: Password Change -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-5 space-y-4">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-[#019D91]/15 text-[#018076] dark:bg-[#019D91]/25 dark:text-[#02b3a5]">
                        Chủ đề phổ biến nhất
                    </span>
                    <h3 class="text-xl sm:text-2xl font-bold text-[#1D1D1F] dark:text-white leading-snug">
                        Đổi mật khẩu tài khoản của bạn
                    </h3>
                    <p class="text-sm text-[#86868B] dark:text-[#7d8590] leading-relaxed">
                        Bạn có thể cập nhật mật khẩu mới bất cứ lúc nào trong mục Hồ sơ cá nhân. Chỉ với 3 bước đơn giản: nhập mật khẩu cũ, đặt mật khẩu mới và bấm Lưu.
                    </p>

                    <div class="space-y-2 pt-2">
                        <div class="flex items-start space-x-3 text-xs sm:text-sm">
                            <span class="w-5 h-5 rounded-full bg-[#019D91] text-white flex items-center justify-center font-bold shrink-0 text-xs">1</span>
                            <span>Mở <a href="{{ route('profile.edit') }}" class="font-semibold text-[#019D91] hover:underline">Hồ sơ cá nhân</a> từ menu người dùng.</span>
                        </div>
                        <div class="flex items-start space-x-3 text-xs sm:text-sm">
                            <span class="w-5 h-5 rounded-full bg-[#019D91] text-white flex items-center justify-center font-bold shrink-0 text-xs">2</span>
                            <span>Chọn mục "Quyền riêng tư" và điền mật khẩu mới.</span>
                        </div>
                        <div class="flex items-start space-x-3 text-xs sm:text-sm">
                            <span class="w-5 h-5 rounded-full bg-[#019D91] text-white flex items-center justify-center font-bold shrink-0 text-xs">3</span>
                            <span>Nhấn "Lưu thay đổi" để hệ thống cập nhật tức thì.</span>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-wrap gap-3">
                        <a href="{{ route('profile.edit') }}#password"
                           class="btn-apple-primary text-xs sm:text-sm px-5 py-2.5">
                            Đi đến Đổi mật khẩu ngay →
                        </a>
                        <a href="{{ route('support.show', 'doi-mat-khau') }}"
                           class="btn-apple-secondary text-xs sm:text-sm px-5 py-2.5">
                            Xem toàn bộ bài viết
                        </a>
                    </div>
                </div>

                <!-- Laptop Mockup Showcase Component -->
                <div class="lg:col-span-7">
                    @include('support.partials.laptop-mockup', ['type' => 'password_change'])
                </div>
            </div>
        </div>
    </section>

    <!-- Frequently Asked Questions (FAQ Section) -->
    <section id="faq" class="max-w-4xl mx-auto px-4 sm:px-6 mb-20">
        <div class="text-center mb-10">
            <h2 class="text-2xl sm:text-3xl font-bold text-[#1D1D1F] dark:text-white tracking-tight">
                Câu hỏi thường gặp
            </h2>
            <p class="text-sm sm:text-base text-[#86868B] dark:text-[#7d8590] mt-1">
                Giải đáp nhanh những câu hỏi hay gặp nhất từ học viên và giảng viên
            </p>
        </div>

        <div class="space-y-3">
            @foreach($faqs as $index => $faq)
                <div class="bg-white dark:bg-[#161b22] rounded-2xl border border-[#E5E5EA] dark:border-[#30363d] overflow-hidden transition-all shadow-sm">
                    <button type="button"
                            @click="activeFaq = (activeFaq === {{ $index }} ? null : {{ $index }})"
                            class="w-full px-5 py-4 text-left flex items-center justify-between gap-4 font-semibold text-sm sm:text-base text-[#1D1D1F] dark:text-white hover:text-[#019D91] dark:hover:text-[#02b3a5] transition">
                        <span>{{ $faq['question'] }}</span>
                        <svg class="w-5 h-5 text-[#86868B] dark:text-[#7d8590] transition-transform duration-200 shrink-0"
                             :class="{ 'rotate-180 text-[#019D91] dark:text-[#02b3a5]': activeFaq === {{ $index }} }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="activeFaq === {{ $index }}"
                         x-cloak
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="px-5 pb-5 pt-1 text-sm text-[#86868B] dark:text-[#7d8590] border-t border-[#E5E5EA]/60 dark:border-[#30363d]/60 leading-relaxed">
                        <p>{{ $faq['answer'] }}</p>
                        @if(!empty($faq['article_slug']))
                            <div class="mt-3">
                                <a href="{{ route('support.show', $faq['article_slug']) }}" class="inline-flex items-center gap-1 font-semibold text-xs text-[#019D91] dark:text-[#02b3a5] hover:underline">
                                    <span>Xem bài hướng dẫn chi tiết</span>
                                    <span>→</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Support Assistance Contact Card -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="bg-white dark:bg-[#161b22] rounded-3xl p-8 sm:p-10 border border-[#E5E5EA] dark:border-[#30363d] shadow-sm text-center">
            <h3 class="text-xl sm:text-2xl font-bold text-[#1D1D1F] dark:text-white">
                Vẫn cần thêm sự trợ giúp?
            </h3>
            <p class="text-sm text-[#86868B] dark:text-[#7d8590] max-w-lg mx-auto mt-2">
                Đội ngũ hỗ trợ kỹ thuật và chăm sóc học viên EduLearn luôn sẵn sàng đồng hành cùng bạn 24/7.
            </p>
            <div class="mt-6 flex flex-wrap justify-center items-center gap-4">
                <a href="{{ route('support.feedback') }}"
                   class="btn-apple-primary px-6 py-3 text-sm inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                    <span>Báo cáo sự cố & Góp ý</span>
                </a>
                <a href="mailto:support@edulearn.vn"
                   class="btn-apple-outline px-6 py-3 text-sm inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>support@edulearn.vn</span>
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
