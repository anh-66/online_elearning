@extends('layouts.public')

@section('title', 'Trang chủ')

@php
    $categoryIcons = [
        'code' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
        'chart-bar' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'palette' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
        'briefcase' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'translate' => 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129',
        'default' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
    ];

    $heroStats = [
        ['value' => $stats['total_courses'], 'label' => 'Khóa học tuyển chọn'],
        ['value' => $stats['total_students'], 'label' => 'Học viên tin tưởng'],
        ['value' => $stats['total_instructors'], 'label' => 'Giảng viên chuyên môn'],
        ['value' => $stats['total_enrollments'], 'label' => 'Lượt ghi danh học tập'],
    ];
@endphp

@section('content')

{{-- ======================================================== --}}
{{-- 1. HERO SECTION (Circuit Background Pattern) --}}
{{-- ======================================================== --}}
<section class="circuit-wrapper relative pt-16 pb-20 sm:pt-24 sm:pb-28 text-center transition-colors overflow-hidden">
    <div class="circuit-background"></div>
    <div class="relative z-10 w-full max-w-5xl mx-auto px-4 sm:px-8">
        {{-- Headline --}}
        <h1 class="font-anton text-5xl sm:text-7xl lg:text-8xl tracking-wide text-[#1D1D1F] dark:text-[#f0f6fc] leading-[1.12] sm:leading-[1.16] uppercase">
            <span class="block">NÂNG TẦM TRI THỨC</span>
            <span class="block mt-3.5 sm:mt-5 lg:mt-6">
                <span class="relative inline-block">
                    {{-- Highlighter accent stripe behind text (matching reference tape/marker aesthetic) --}}
                    <span class="absolute -inset-x-3 sm:-inset-x-5 bottom-1.5 sm:bottom-2.5 lg:bottom-3.5 h-4 sm:h-7 lg:h-9 bg-[#ffe17c] dark:bg-[#ffe17c] -rotate-[0.8deg] rounded-sm -z-10 pointer-events-none" aria-hidden="true"></span>
                    <span class="relative z-10">KHỞI ĐẦU BỨT PHÁ</span>
                </span>
            </span>
        </h1>

        {{-- Subheadline --}}
        <p class="mt-5 text-lg sm:text-2xl text-[#86868B] dark:text-[#8b949e] font-normal max-w-3xl mx-auto leading-relaxed">
            Học lập trình, dữ liệu và công nghệ với lộ trình rõ ràng, theo sát tiến độ cùng các chuyên gia hàng đầu.
        </p>

        {{-- Centered Search Input Pill (with GitHub Dark border & surface) --}}
        <div class="mt-10 max-w-2xl mx-auto">
            <form action="{{ route('courses.index') }}" method="GET" class="relative">
                <input type="text" name="keyword" placeholder="Tìm kiếm công nghệ, kỹ năng, khóa học..."
                       class="w-full h-14 pl-14 pr-36 text-sm sm:text-base bg-[#F5F5F7] dark:bg-[#161b22] text-[#1D1D1F] dark:text-[#f0f6fc] placeholder-[#86868B] dark:placeholder-[#7d8590] border border-[#E5E5EA] dark:border-[#30363d] rounded-2xl focus:outline-none focus:ring-2 focus:ring-[#019D91] dark:focus:border-[#02b3a5] transition">
                <span class="absolute left-5 top-4 text-[#86868B] dark:text-[#7d8590]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <button type="submit" class="absolute right-2 top-2 bottom-2 px-6 bg-[#019D91] hover:bg-[#008b80] text-white text-xs sm:text-sm font-semibold rounded-xl transition shadow-sm active:scale-[0.98]">
                    Tìm kiếm
                </button>
            </form>
        </div>

        {{-- Symmetrical Stats Rail (Clean Open Typography: Big Numbers & Small Labels, No Boxes) --}}
        <div class="mt-16 grid grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-10 max-w-4xl mx-auto">
            @foreach($heroStats as $stat)
                <div class="text-center">
                    <p class="font-anton text-5xl sm:text-6xl lg:text-7xl text-[#1D1D1F] dark:text-[#f0f6fc] leading-none">
                        {{ number_format($stat['value']) }}+
                    </p>
                    <p class="mt-3 text-sm sm:text-base font-normal text-[#86868B] dark:text-[#8b949e]">
                        {{ $stat['label'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ======================================================== --}}
{{-- 2. BILLBOARD BANNER (Full Width Edge-to-Edge) --}}
{{-- ======================================================== --}}
@if($banners->isNotEmpty())
<section class="w-full relative overflow-hidden select-none border-y border-[#30363d]" 
         x-data="{ current: 0, total: {{ $banners->count() }} }" 
         x-init="total > 1 && setInterval(() => { current = (current + 1) % total }, 6000)">
    
    {{-- Banner Slider: Tràn viền toàn diện, thay thế hoàn toàn ô màu đen cũ --}}
    <div class="relative w-full h-[260px] sm:h-[360px] md:h-[420px] lg:h-[480px] overflow-hidden">
        @foreach($banners as $index => $banner)
            <div class="absolute inset-0 w-full h-full transition-opacity duration-700 ease-in-out"
                 x-show="current === {{ $index }}"
                 x-cloak
                 style="@if(!$banner->image_path && $banner->bg_color) background: {{ $banner->bg_color }}; @endif">
                
                @if($banner->image_path)
                    <a href="{{ $banner->link_url ? url($banner->link_url) : 'javascript:void(0)' }}" 
                       class="block w-full h-full relative group">
                        <img src="{{ Storage::disk('public')->url($banner->image_path) }}"
                             alt="{{ $banner->title }}"
                             class="w-full h-full object-cover object-center"
                             loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                             onerror="this.style.display='none'; this.nextElementSibling?.classList.replace('hidden', 'flex');">
                        
                        {{-- Fallback nếu ảnh bị lỗi đường dẫn --}}
                        <div class="hidden absolute inset-0 w-full h-full items-center justify-center p-6 text-center"
                             style="background: {{ $banner->bg_color ?: '#019D91' }};">
                            <h2 class="banner-headline font-anton uppercase tracking-wide text-2xl sm:text-4xl lg:text-5xl text-white leading-[1.2] sm:leading-[1.28] drop-shadow-lg max-w-4xl mx-auto">
                                {{ $banner->title }}
                            </h2>
                        </div>

                        {{-- Optional Text Overlay: Chỉ hiển thị nếu admin bật show_text --}}
                        @if($banner->show_text)
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent flex items-center justify-center p-6 text-center">
                                <div class="max-w-4xl mx-auto space-y-3 sm:space-y-4">
                                    <h2 class="banner-headline font-anton uppercase tracking-wide text-2xl sm:text-4xl lg:text-5xl text-white leading-[1.2] sm:leading-[1.28] drop-shadow-lg max-w-4xl mx-auto">
                                        {{ $banner->title }}
                                    </h2>
                                    <p class="text-xs sm:text-base text-white/90 max-w-xl mx-auto leading-relaxed drop-shadow">
                                        Khám phá khóa học chuyên sâu được cập nhật liên tục theo chuẩn doanh nghiệp.
                                    </p>
                                    @if($banner->link_url)
                                        <div class="pt-1">
                                            <span class="inline-flex items-center gap-1.5 px-6 sm:px-7 py-2.5 sm:py-3 rounded-full bg-white text-[#161b22] text-xs sm:text-sm font-bold shadow-xl group-hover:bg-neutral-100 transition">
                                                Khám phá chương trình &rarr;
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </a>
                @else
                    {{-- Color Only Banner (Toàn màn hình lấp đầy bằng màu/gradient và chữ) --}}
                    <div class="w-full h-full flex items-center justify-center p-6 sm:p-12 text-center text-white relative"
                         style="background: {{ $banner->bg_color ?: '#019D91' }};">
                        
                        {{-- Subtle dot pattern --}}
                        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>

                        <div class="relative z-10 max-w-4xl mx-auto space-y-3 sm:space-y-4">
                            <h2 class="banner-headline font-anton uppercase tracking-wide text-3xl sm:text-5xl lg:text-6xl text-white leading-[1.2] sm:leading-[1.28] drop-shadow-md max-w-4xl mx-auto">
                                {{ $banner->title }}
                            </h2>
                            <p class="text-sm sm:text-lg text-white/90 max-w-2xl mx-auto leading-relaxed drop-shadow">
                                Khám phá khóa học chuyên sâu được cập nhật liên tục theo chuẩn doanh nghiệp.
                            </p>
                            @if($banner->link_url)
                                <div class="pt-2">
                                    <a href="{{ url($banner->link_url) }}" class="inline-flex items-center justify-center rounded-full bg-white text-[#161b22] hover:bg-neutral-100 px-8 py-3.5 text-xs sm:text-sm font-bold shadow-xl transition active:scale-95">
                                        Khám phá chương trình &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Left / Right Slider Arrows --}}
    @if($banners->count() > 1)
        <button type="button" @click="current = (current - 1 + total) % total" aria-label="Banner trước"
                class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 w-10 sm:w-12 h-10 sm:h-12 rounded-full bg-black/40 hover:bg-black/70 text-white backdrop-blur-md flex items-center justify-center transition shadow-lg z-20">
            <svg class="w-5 sm:w-6 h-5 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button type="button" @click="current = (current + 1) % total" aria-label="Banner sau"
                class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 w-10 sm:w-12 h-10 sm:h-12 rounded-full bg-black/40 hover:bg-black/70 text-white backdrop-blur-md flex items-center justify-center transition shadow-lg z-20">
            <svg class="w-5 sm:w-6 h-5 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </button>
    @endif

    {{-- Dots Indicator --}}
    @if($banners->count() > 1)
        <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2.5 z-20">
            @foreach($banners as $index => $banner)
                <button type="button" @click="current = {{ $index }}" aria-label="Banner {{ $index + 1 }}"
                        class="h-2 rounded-full transition-all duration-300 drop-shadow"
                        :class="current === {{ $index }} ? 'bg-white w-8' : 'bg-white/40 w-2.5 hover:bg-white/70'">
                </button>
            @endforeach
        </div>
    @endif
</section>
@endif

{{-- ======================================================== --}}
{{-- 2.5. HOW IT WORKS / START LEARNING (Lộ trình 3 bước) --}}
{{-- ======================================================== --}}
<section class="bg-white dark:bg-[#0d1117] py-16 sm:py-24 border-b border-neutral-200/60 dark:border-neutral-800/60 transition-colors">
    <div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-8 lg:px-12">
        {{-- Two-column layout (1:2 ratio) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 lg:gap-16 items-start">
            {{-- Left column: Sticky 'Anton' title (text-7xl) --}}
            <div class="lg:col-span-1 lg:sticky lg:top-28">
                <h2 class="font-anton text-6xl sm:text-7xl text-[#1D1D1F] dark:text-white uppercase leading-[1.15] tracking-wide">
                    <span class="block">BẮT ĐẦU</span>
                    <span class="block mt-2 sm:mt-3">BỨT PHÁ</span>
                </h2>
                <p class="mt-6 text-base text-[#86868B] dark:text-[#8b949e] max-w-sm leading-relaxed">
                    Phương pháp học tập tinh gọn giúp bạn nắm vững kiến thức thực chiến và nhanh chóng đạt mục tiêu nghề nghiệp.
                </p>
            </div>

            {{-- Right column: Vertical stack of three steps --}}
            <div class="lg:col-span-2 flex flex-col divide-y divide-neutral-200/80 dark:divide-neutral-800/80 border-t border-b border-neutral-200/80 dark:border-neutral-800/80">
                {{-- Step 01 --}}
                <div class="py-8 sm:py-10 flex items-start gap-6 sm:gap-10 group cursor-pointer transition-colors hover:bg-neutral-50/50 dark:hover:bg-neutral-900/30 px-3 sm:px-5 -mx-3 sm:-mx-5 rounded-2xl">
                    {{-- Massive 8xl numeral in #ffe17c/20% that changes to full opacity on hover --}}
                    <span class="font-anton text-7xl sm:text-8xl text-[#ffe17c] opacity-20 group-hover:opacity-100 group-hover:scale-105 leading-none shrink-0 w-24 sm:w-32 select-none tracking-tight transition-all duration-300">
                        01
                    </span>
                    <div class="flex-1 pt-1 sm:pt-2">
                        {{-- 'Anton' title --}}
                        <h3 class="font-anton text-xl sm:text-2xl tracking-wide text-[#1D1D1F] dark:text-white uppercase group-hover:text-[#019D91] dark:group-hover:text-[#02b3a5] transition-colors">
                            CHỌN LỘ TRÌNH PHÙ HỢP
                        </h3>
                        <p class="mt-2 text-sm sm:text-base text-[#86868B] dark:text-[#8b949e] leading-relaxed">
                            Khám phá hàng trăm khóa học lập trình, dữ liệu và công nghệ được thiết kế bài bản từ nền tảng đến chuyên sâu theo tiêu chuẩn doanh nghiệp.
                        </p>
                    </div>
                </div>

                {{-- Step 02 --}}
                <div class="py-8 sm:py-10 flex items-start gap-6 sm:gap-10 group cursor-pointer transition-colors hover:bg-neutral-50/50 dark:hover:bg-neutral-900/30 px-3 sm:px-5 -mx-3 sm:-mx-5 rounded-2xl">
                    <span class="font-anton text-7xl sm:text-8xl text-[#ffe17c] opacity-20 group-hover:opacity-100 group-hover:scale-105 leading-none shrink-0 w-24 sm:w-32 select-none tracking-tight transition-all duration-300">
                        02
                    </span>
                    <div class="flex-1 pt-1 sm:pt-2">
                        <h3 class="font-anton text-xl sm:text-2xl tracking-wide text-[#1D1D1F] dark:text-white uppercase group-hover:text-[#019D91] dark:group-hover:text-[#02b3a5] transition-colors">
                            LÀM CHỦ NHỊP ĐỘ HỌC
                        </h3>
                        <p class="mt-2 text-sm sm:text-base text-[#86868B] dark:text-[#8b949e] leading-relaxed">
                            Học mọi lúc mọi nơi trên đa thiết bị. Tương tác trực tiếp qua bài giảng video chất lượng cao, bài tập thực hành bám sát thực tế cùng hỗ trợ từ giảng viên.
                        </p>
                    </div>
                </div>

                {{-- Step 03 --}}
                <div class="py-8 sm:py-10 flex items-start gap-6 sm:gap-10 group cursor-pointer transition-colors hover:bg-neutral-50/50 dark:hover:bg-neutral-900/30 px-3 sm:px-5 -mx-3 sm:-mx-5 rounded-2xl">
                    <span class="font-anton text-7xl sm:text-8xl text-[#ffe17c] opacity-20 group-hover:opacity-100 group-hover:scale-105 leading-none shrink-0 w-24 sm:w-32 select-none tracking-tight transition-all duration-300">
                        03
                    </span>
                    <div class="flex-1 pt-1 sm:pt-2">
                        <h3 class="font-anton text-xl sm:text-2xl tracking-wide text-[#1D1D1F] dark:text-white uppercase group-hover:text-[#019D91] dark:group-hover:text-[#02b3a5] transition-colors">
                            TẠO NÊN SẢN PHẨM THỰC TẾ
                        </h3>
                        <p class="mt-2 text-sm sm:text-base text-[#86868B] dark:text-[#8b949e] leading-relaxed">
                            Xây dựng sản phẩm hoàn chỉnh để đưa vào hồ sơ năng lực (Portfolio), nhận chứng chỉ hoàn thành khóa học và tự tin bứt phá trong sự nghiệp.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ======================================================== --}}
{{-- 3. DANH MỤC NỔI BẬT --}}
{{-- ======================================================== --}}
@if($featuredCategories->isNotEmpty())
<section class="bg-white dark:bg-[#0d1117] py-16 sm:py-20 transition-colors">
    <div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-8 lg:px-12">
        <div class="mb-8">
            <h2 class="font-anton uppercase tracking-wide text-2xl sm:text-4xl text-[#1D1D1F] dark:text-[#f0f6fc]">
                Danh mục chuyên ngành
            </h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 sm:gap-6">
            @foreach($featuredCategories as $category)
                @php($iconPath = $categoryIcons[$category->icon] ?? null)
                <a href="{{ route('courses.index', ['category' => $category->id]) }}"
                   class="group p-6 bg-[#F5F5F7] dark:bg-[#161b22] border border-[#E5E5EA] dark:border-[#30363d] rounded-3xl transition duration-200 hover:bg-[#EAEAEA] dark:hover:bg-[#21262d] dark:hover:border-[#8b949e]/50 flex flex-col justify-between">
                    <div class="w-12 h-12 rounded-full bg-white dark:bg-[#0d1117] text-[#1D1D1F] dark:text-[#e6edf3] flex items-center justify-center mb-5 border border-black/5 dark:border-[#30363d] shadow-sm">
                        @if($category->icon && ! $iconPath && ! preg_match('/^[a-z0-9-]+$/', $category->icon))
                            <span class="text-xl">{{ $category->icon }}</span>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $iconPath ?? $categoryIcons['default'] }}"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <p class="font-semibold text-base sm:text-lg text-[#1D1D1F] dark:text-[#f0f6fc] group-hover:text-[#019D91] dark:group-hover:text-[#02b3a5] transition-colors truncate">
                            {{ $category->name }}
                        </p>
                        <p class="text-xs sm:text-sm text-[#86868B] dark:text-[#8b949e] mt-1">
                            {{ $category->courses_count }} khóa học
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ======================================================== --}}
{{-- 4. KHÓA HỌC NỔI BẬT --}}
{{-- ======================================================== --}}
@if($featuredCourses->isNotEmpty())
<section class="bg-white dark:bg-[#0d1117] py-16 sm:py-20 transition-colors">
    <div class="w-full max-w-screen-2xl mx-auto px-4 sm:px-8 lg:px-12">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-3">
            <div>
                <h2 class="font-anton uppercase tracking-wide text-2xl sm:text-4xl text-[#1D1D1F] dark:text-[#f0f6fc]">
                    Khóa học được lựa chọn nhiều nhất
                </h2>
            </div>
            <a href="{{ route('courses.index') }}" class="text-sm font-medium text-[#019D91] dark:text-[#02b3a5] hover:underline flex items-center gap-1">
                Xem toàn bộ khóa học <span>&rsaquo;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 sm:gap-6">
            @foreach($featuredCourses as $course)
                @include('partials.course-card', ['course' => $course])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ======================================================== --}}
{{-- 5. CALL TO ACTION (Floating Tech Icons - Natural Proportions & Centered Container) --}}
{{-- ======================================================== --}}
<section class="relative bg-white dark:bg-[#0d1117] py-16 sm:py-24 text-center transition-colors overflow-hidden">
    {{-- Centered Canvas (Chứa text và các icon bao quanh tập trung, hai bên mép có khoảng trống tự nhiên như ảnh mẫu) --}}
    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 min-h-[440px] flex items-center justify-center">

        {{-- ================= VÙNG ICON TĨNH PHÂN BỔ TO NHỎ ================= --}}
        <div class="tech-icons pointer-events-none select-none" aria-hidden="true">

            {{-- -------- CỤM BÊN TRÁI -------- --}}
            {{-- 1. Google (Vừa ~ 36px, nude icon đa sắc như ảnh mẫu góc trên trái) --}}
            <div class="tech-float hidden sm:block absolute top-2 left-[3%] lg:left-[5%]">
                <svg class="w-9 h-9" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
            </div>

            {{-- 2. React (Vừa ~ 38px, nude cyan) --}}
            <div class="tech-float hidden md:block absolute top-1 left-[25%] lg:left-[26%]">
                <svg class="w-10 h-10 text-[#00D8FF]" viewBox="-11.5 -10.23174 23 20.46348" fill="none" stroke="currentColor" stroke-width="1.3">
                    <circle cx="0" cy="0" r="2.05" fill="#00D8FF"/>
                    <ellipse rx="11" ry="4.2"/>
                    <ellipse rx="11" ry="4.2" transform="rotate(60)"/>
                    <ellipse rx="11" ry="4.2" transform="rotate(120)"/>
                </svg>
            </div>

            {{-- 3. PHP (Vừa ~ 40px x 24px) --}}
            <div class="tech-float hidden sm:block absolute top-16 left-[14%] lg:left-[15%]">
                <svg class="w-11 h-6" viewBox="0 0 128 128">
                    <ellipse cx="64" cy="64" rx="60" ry="34" fill="#777BB4"/>
                    <text x="64" y="74" font-size="34" font-family="system-ui, sans-serif" font-weight="bold" fill="#ffffff" text-anchor="middle" font-style="italic">php</text>
                </svg>
            </div>

            {{-- 4. Figma (TO NỔI BẬT ~ 52px, app icon bo góc y chang mẫu) --}}
            <div class="tech-float hidden sm:flex absolute top-[40%] left-[2%] lg:left-[3%] w-13 h-13 rounded-2xl bg-black dark:bg-[#161b22] border border-black/10 dark:border-[#30363d] items-center justify-center shadow-lg p-2.5">
                <svg class="w-8 h-8" viewBox="0 0 38 57" fill="none">
                    <path d="M19 28.5C19 23.2533 23.2533 19 28.5 19C33.7467 19 38 23.2533 38 28.5C38 33.7467 33.7467 38 28.5 38C23.2533 38 19 33.7467 19 28.5Z" fill="#1ABCFE"/>
                    <path d="M0 47.5C0 42.2533 4.25329 38 9.5 38H19V47.5C19 52.7467 14.7467 57 9.5 57C4.25329 57 0 52.7467 0 47.5Z" fill="#0ACF83"/>
                    <path d="M19 0V19H28.5C33.7467 19 38 14.7467 38 9.5C38 4.25329 33.7467 0 28.5 0H19Z" fill="#FF7262"/>
                    <path d="M0 9.5C0 14.7467 4.25329 19 9.5 19H19V0H9.5C4.25329 0 0 4.25329 0 9.5Z" fill="#F24E1E"/>
                    <path d="M0 28.5C0 33.7467 4.25329 38 9.5 38H19V19H9.5C4.25329 19 0 23.2533 0 28.5Z" fill="#A259FF"/>
                </svg>
            </div>

            {{-- 5. JavaScript (Nhỏ vừa ~ 30px, dịch xa ra khỏi khối chữ) --}}
            <div class="tech-float hidden md:flex absolute top-[52%] left-[10%] lg:left-[12%] w-8 h-8 rounded-lg bg-[#F7DF1E] text-black font-black text-xs items-center justify-center shadow-sm">
                JS
            </div>

            {{-- 6. HTML5 (Nhỏ ~ 24px) --}}
            <div class="tech-float hidden lg:block absolute bottom-14 left-[6%]">
                <svg class="w-6 h-6" viewBox="0 0 512 512">
                    <path fill="#E34F26" d="M71 460L30 0h452l-41 460-185 52z"/>
                    <path fill="#EF652A" d="M256 472l149-41 35-391H256v432z"/>
                    <path fill="#EBEBEB" d="M256 208h-68l-5-53h73V97H114l14 163h128v-52zm0 147l-63-17-4-45h-53l8 94 112 31v-63z"/>
                    <path fill="#FFF" d="M256 208v-53h68l6-58H256V44h142l-13 147H256v17zm0 147v63l112-31 16-177H256v52h64l-6 76-58 17z"/>
                </svg>
            </div>

            {{-- 7. Python (Vừa ~ 34px) --}}
            <div class="tech-float hidden sm:block absolute bottom-4 left-[14%] lg:left-[16%]">
                <svg class="w-8 h-8" viewBox="0 0 128 128">
                    <path fill="#3776AB" d="M63.7 3.5c-15.5 0-25 6.7-25 19.8v14.5h25.4v3.6H27.9C13 41.4 3.5 50.8 3.5 65.7c0 14.8 8.4 24.3 22.8 24.3h8.3v-11.7c0-8.7 7.7-16.1 16.4-16.1h25.3c7.2 0 13.1-6 13.1-13.3V23.3c0-12.8-10.4-19.8-25.7-19.8zm-13.8 7.3a4.2 4.2 0 1 1 0 8.4 4.2 4.2 0 0 1 0-8.4z"/>
                    <path fill="#FFD43B" d="M64.3 124.5c15.5 0 25-6.7 25-19.8V90.2H63.9v-3.6h36.2c14.9 0 24.4-9.4 24.4-24.3 0-14.8-8.4-24.3-22.8-24.3h-8.3v11.7c0 8.7-7.7 16.1-16.4 16.1H51.7c-7.2 0-13.1 6-13.1 13.3v25.6c0 12.8 10.4 19.8 25.7 19.8zm13.8-7.3a4.2 4.2 0 1 1 0-8.4 4.2 4.2 0 0 1 0 8.4z"/>
                </svg>
            </div>

            {{-- 8. Git (Nhỏ ~ 24px, hình thoi cam) --}}
            <div class="tech-float hidden md:block absolute bottom-8 left-[27%]">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="#F05032">
                    <path d="M21.62 11.11l-8.73-8.73a1.76 1.76 0 00-2.48 0L8.6 4.19l3.14 3.14a2.09 2.09 0 012.37 2.37l3.03 3.03a2.09 2.09 0 011.66 2.45 2.1 2.1 0 01-2.44-1.67l-2.82-2.82v5.33a2.12 2.12 0 11-1.75 0V10.4a2.11 2.11 0 01-1.15-1.84 2.1 2.1 0 01.37-1.18L7.76 4.23 2.38 9.61a1.76 1.76 0 000 2.48l8.73 8.73a1.76 1.76 0 002.48 0l8.03-8.03a1.76 1.76 0 000-2.48z"/>
                </svg>
            </div>

            {{-- -------- CỤM BÊN PHẢI -------- --}}
            {{-- 9. YouTube (TO NỔI BẬT ~ 46px x 32px, nút Play đỏ) --}}
            <div class="tech-float hidden sm:block absolute top-2 right-[20%] lg:right-[22%]">
                <svg class="w-12 h-8" viewBox="0 0 24 24">
                    <path fill="#FF0000" d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814z"/>
                    <path fill="#FFFFFF" d="m9.545 15.568 6.273-3.568-6.273-3.568z"/>
                </svg>
            </div>

            {{-- 10. Tailwind CSS (Nhỏ vừa ~ 30px, sóng cyan) --}}
            <div class="tech-float hidden md:block absolute top-2 right-[4%] lg:right-[6%]">
                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="#38BDF8">
                    <path d="M12.001 4.8c-3.2 0-5.2 1.6-6 4.8 1.2-1.6 2.6-2.2 4.2-1.8.913.228 1.565.89 2.288 1.624C13.666 10.618 15.027 12 18.001 12c3.2 0 5.2-1.6 6-4.8-1.2 1.6-2.6 2.2-4.2 1.8-.913-.228-1.565-.89-2.288-1.624C16.337 6.182 14.976 4.8 12.001 4.8zm-6 7.2c-3.2 0-5.2 1.6-6 4.8 1.2-1.6 2.6-2.2 4.2-1.8.913.228 1.565.89 2.288 1.624 1.177 1.194 2.538 2.576 5.512 2.576 3.2 0 5.2-1.6 6-4.8-1.2 1.6-2.6 2.2-4.2 1.8-.913-.228-1.565-.89-2.288-1.624C10.337 13.382 8.976 12 6.001 12z"/>
                </svg>
            </div>

            {{-- 11. Laravel (Vừa ~ 36px, khối 3D đỏ) --}}
            <div class="tech-float hidden sm:block absolute top-16 right-[10%] lg:right-[12%]">
                <svg class="w-9 h-9" viewBox="0 0 24 24" fill="#FF2D20">
                    <path d="M8.27 11.23L2.64 8.01a.75.75 0 010-1.3L12.07.72a.75.75 0 01.76 0l9.43 5.99a.75.75 0 010 1.3l-5.63 3.22 5.63 3.22a.75.75 0 010 1.3l-9.43 5.99a.75.75 0 01-.76 0L2.64 15.75a.75.75 0 010-1.3l5.63-3.22zm3.73-8.8L4.69 7.36l3.58 2.05 7.31-4.17-3.58-2.81zm-7.31 12.35l7.31 4.65 7.31-4.65-3.58-2.05-7.31 4.17-3.73-2.12zm15.42-7.42l-3.73-2.13-7.31 4.17 3.58 2.05 7.46-4.09z"/>
                </svg>
            </div>

            {{-- 12. VS Code (TO NỔI BẬT ~ 52px, app icon xanh bo góc như Ae trong mẫu) --}}
            <div class="tech-float hidden sm:flex absolute top-[38%] right-[2%] lg:right-[3%] w-13 h-13 rounded-2xl bg-[#007ACC] items-center justify-center shadow-lg p-2.5">
                <svg class="w-8 h-8 fill-white" viewBox="0 0 24 24">
                    <path d="M17.472 2.012a1.488 1.488 0 00-.737.199L9.04 7.625l-4.49-3.414a.994.994 0 00-1.42.348l-1.002 1.62a1 1 0 00.273 1.348l3.961 3.01-3.96 3.01a.999.999 0 00-.274 1.349l1.002 1.62a.996.996 0 001.42.348l4.49-3.414 7.695 5.414a1.496 1.496 0 001.597.108c.524-.298.848-.857.848-1.46V3.58c0-.603-.324-1.162-.848-1.46a1.47 1.47 0 00-.86-.108zM17.2 6.555v10.89l-5.698-4.008 5.698-6.882z"/>
                </svg>
            </div>

            {{-- 13. GitHub (Vừa ~ 38px, dịch xa ra khỏi khối chữ) --}}
            <div class="tech-float hidden md:flex absolute top-[52%] right-[10%] lg:right-[12%] w-10 h-10 rounded-xl bg-[#181717] dark:bg-[#161b22] text-white items-center justify-center shadow-md border border-black/10 dark:border-[#30363d]">
                <svg class="w-6 h-6 fill-white" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                </svg>
            </div>

            {{-- 14. CSS3 (Nhỏ ~ 24px) --}}
            <div class="tech-float hidden lg:block absolute bottom-14 right-[15%]">
                <svg class="w-6 h-6" viewBox="0 0 512 512">
                    <path fill="#1572B6" d="M71 460L30 0h452l-41 460-185 52z"/>
                    <path fill="#33A9DC" d="M256 472l149-41 35-391H256v432z"/>
                    <path fill="#EBEBEB" d="M256 208h-68l-5-53h73V97H114l14 163h128v-52zm0 147l-63-17-4-45h-53l8 94 112 31v-63z"/>
                    <path fill="#FFF" d="M256 97h136l-5 58H256v53h68l-7 76-61 17v63l112-31 16-177h-128V97z"/>
                </svg>
            </div>

            {{-- 15. Docker (Vừa ~ 36px) --}}
            <div class="tech-float hidden sm:block absolute bottom-4 right-[24%] lg:right-[26%]">
                <svg class="w-9 h-9" viewBox="0 0 24 24" fill="#2496ED">
                    <path d="M13.983 11.078h2.119a.186.186 0 00.186-.185V9.006a.186.186 0 00-.186-.186h-2.119a.185.185 0 00-.185.185v1.888c0 .102.083.185.185.185m-2.954-5.43h2.118a.186.186 0 00.186-.186V3.574a.186.186 0 00-.186-.185h-2.118a.185.185 0 00-.185.185v1.888c0 .102.082.185.185.185m0 2.716h2.118a.187.187 0 00.186-.186V6.29a.186.186 0 00-.186-.185h-2.118a.185.185 0 00-.185.185v1.887c0 .102.082.186.185.186m-2.93 0h2.12a.186.186 0 00.184-.186V6.29a.185.185 0 00-.185-.185H8.1a.185.185 0 00-.185.185v1.887c0 .102.083.186.185.186m-2.954 0h2.119a.186.186 0 00.185-.186V6.29a.185.185 0 00-.185-.185H5.145a.185.185 0 00-.185.185v1.887c0 .102.083.186.185.186m5.884 2.714h2.118a.186.186 0 00.186-.185V9.006a.186.186 0 00-.186-.186h-2.118a.185.185 0 00-.185.185v1.888c0 .102.082.185.185.185m-2.93 0h2.12a.185.185 0 00.184-.185V9.006a.185.185 0 00-.184-.186H8.1a.185.185 0 00-.185.185v1.888c0 .102.083.185.185.185m-2.954 0h2.119a.185.185 0 00.185-.185V9.006a.185.185 0 00-.185-.186H5.145a.185.185 0 00-.185.185v1.888c0 .102.083.185.185.185m-2.93 0h2.12a.185.185 0 00.184-.185V9.006a.185.185 0 00-.184-.186h-2.12a.185.185 0 00-.184.185v1.888c0 .102.083.185.185.185M23.763 9.89c-.065-.051-.672-.51-1.954-.51-.338.001-.676.03-1.01.087-.248-1.7-1.653-2.53-1.716-2.566l-.344-.199-.226.327c-.284.438-.49.922-.612 1.432-.23-.07-.47-.119-.714-.146-.607-.068-1.57-.02-2.421.654l-.454.36.27.525c.89 1.733.642 3.864-.672 5.342-.906.993-2.193 1.543-3.528 1.507H1.546a.855.855 0 00-.853.856c-.005 1.516.48 2.996 1.382 4.218 1.765 2.378 4.607 3.655 8.448 3.795 5.864.213 11.233-2.073 13.064-5.918.064-.132.146-.358.232-.619.68-2.062 1.144-4.82-.056-8.788"/>
                </svg>
            </div>

            {{-- 16. TypeScript (Nhỏ ~ 24px) --}}
            <div class="tech-float hidden sm:flex absolute bottom-6 right-[5%] lg:right-[7%] w-6 h-6 rounded-md bg-[#3178C6] text-white font-black text-[10px] items-center justify-center shadow-sm">
                TS
            </div>
        </div>

        {{-- ================= KHỐI NỘI DUNG CHÍNH (Chính giữa) ================= --}}
        <div class="relative z-10 max-w-xl mx-auto px-4 py-8">
            <h2 class="font-anton uppercase tracking-wide text-3xl sm:text-5xl text-[#1D1D1F] dark:text-[#f0f6fc] leading-[1.2] sm:leading-[1.25]">
                <span class="block">BẮT ĐẦU HÀNH TRÌNH HỌC</span>
                <span class="block mt-2 sm:mt-3">TẬP NGAY HÔM NAY</span>
            </h2>
            <p class="mt-4 text-base sm:text-lg text-[#86868B] dark:text-[#8b949e] max-w-md sm:max-w-lg mx-auto leading-relaxed">
                Hàng chục khóa học chất lượng cao đang chờ đón bạn. Đăng ký dễ dàng, học mọi lúc mọi nơi.
            </p>
            <div class="mt-8 flex justify-center">
                @guest
                    <a href="{{ route('register') }}" class="btn-apple-primary px-9 py-3.5 text-base font-medium">
                        Tạo tài khoản miễn phí
                    </a>
                @else
                    <a href="{{ route('courses.index') }}" class="btn-apple-primary px-9 py-3.5 text-base font-medium">
                        Khám phá thêm khóa học
                    </a>
                @endguest
            </div>
        </div>

    </div>
</section>

{{-- ======================================================== --}}
{{-- 6. BECOME AN INSTRUCTOR / WORK EMAIL CTA (Clean Pure White Theme) --}}
{{-- ======================================================== --}}
<section class="relative bg-white dark:bg-[#0d1117] pt-16 pb-20 sm:pt-24 sm:pb-28 lg:pt-28 lg:pb-32 text-center overflow-hidden transition-colors">
    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-8">
        {{-- Massive Headline in Anton with proper vertical clearance and highlighter stripe --}}
        <h2 class="font-anton uppercase text-5xl sm:text-7xl lg:text-8xl text-[#1D1D1F] dark:text-[#f0f6fc] leading-[1.12] sm:leading-[1.16] tracking-tight">
            <span class="block">ĐỒNG HÀNH GIẢNG DẠY</span>
            <span class="block mt-3.5 sm:mt-5 lg:mt-6">
                <span class="relative inline-block">
                    {{-- Highlighter accent stripe behind text (matching KHỞI ĐẦU BỨT PHÁ) --}}
                    <span class="absolute -inset-x-3 sm:-inset-x-5 bottom-1.5 sm:bottom-2.5 lg:bottom-3.5 h-4 sm:h-7 lg:h-9 bg-[#ffe17c] dark:bg-[#ffe17c] -rotate-[0.8deg] rounded-sm -z-10 pointer-events-none" aria-hidden="true"></span>
                    <span class="relative z-10">LAN TỎA TRI THỨC</span>
                </span>
            </span>
        </h2>

        {{-- Subheadline --}}
        <p class="mt-5 sm:mt-6 text-base sm:text-xl text-[#86868B] dark:text-[#8b949e] font-normal max-w-2xl mx-auto leading-relaxed">
            Gia nhập cộng đồng giảng viên chuyên gia, chia sẻ kiến thức thực chiến và tạo dấu ấn cùng hàng ngàn học viên.
        </p>

        {{-- Email Subscription Input Bar (Clean Apple / GitHub Dark Tokens) --}}
        <div class="mt-8 sm:mt-10 max-w-lg mx-auto" x-data="{ email: '', submitted: false }">
            <form @submit.prevent="if(email) { submitted = true; setTimeout(() => { submitted = false; email = ''; }, 4500); }"
                  class="flex flex-col sm:flex-row gap-3 items-stretch">
                <input type="email"
                       x-model="email"
                       required
                       placeholder="Your work email"
                       class="flex-1 h-14 sm:h-16 px-5 sm:px-6 rounded-xl sm:rounded-2xl bg-[#F5F5F7] dark:bg-[#161b22] text-[#1D1D1F] dark:text-[#f0f6fc] placeholder-[#86868B] dark:placeholder-[#7d8590] border border-[#E5E5EA] dark:border-[#30363d] focus:outline-none focus:ring-2 focus:ring-[#019D91] dark:focus:border-[#02b3a5] text-sm sm:text-base shadow-sm transition">
                <button type="submit"
                        class="h-14 sm:h-16 px-7 sm:px-9 bg-[#019D91] hover:bg-[#008b80] text-white rounded-xl sm:rounded-2xl font-anton uppercase text-sm sm:text-base tracking-wide flex items-center justify-center transition active:scale-[0.98] shadow-md hover:shadow-lg shrink-0">
                    GỬI LIÊN HỆ
                </button>
            </form>

            {{-- Decorative Success Message --}}
            <div x-show="submitted"
                 x-cloak
                 x-transition
                 class="mt-4 p-3.5 bg-[#019D91]/10 border border-[#019D91]/20 text-[#019D91] dark:text-[#02b3a5] text-xs sm:text-sm font-semibold rounded-xl text-center">
                ✓ Cảm ơn bạn! Đội ngũ tuyển dụng giảng viên EduLearn sẽ liên hệ qua email của bạn sớm nhất.
            </div>
        </div>
    </div>
</section>

@endsection
