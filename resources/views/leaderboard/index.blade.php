@extends('layouts.public')

@section('title', 'Bảng Xếp Hạng Điểm Tích Lũy - EduLearn')

@section('content')
<style>
    /* Avatar Frame Animation System (Chỉ áp dụng riêng cho Bảng Xếp Hạng Top 3) */
    .avatar-badge-box {
        position: relative;
        width: var(--avatar-size, 80px);
        height: var(--avatar-size, 80px);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .avatar-badge-box .avatar-circle {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        overflow: hidden;
        position: relative;
        z-index: 10;
        background-color: #000;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        user-select: none;
    }
    .avatar-badge-box .avatar-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .avatar-badge-box .avatar-badge-lottie {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 326.8%; /* 1000 / 306 = 3.268 theo tỷ lệ thiết kế khung game */
        height: 193.1%; /* 591 / 306 = 1.931 */
        pointer-events: none;
        z-index: 20;
    }
    .avatar-badge-box .avatar-badge-lottie svg {
        width: 100% !important;
        height: 100% !important;
        overflow: visible !important;
    }
</style>

<div class="min-h-screen bg-white dark:bg-[#0d1117] text-[#111827] dark:text-neutral-100 flex flex-col">
    <main class="flex-1 overflow-y-auto pb-20">
        <!-- Title Section -->
        <section class="max-w-6xl mx-auto px-4 sm:px-8 pt-8 sm:pt-12 pb-6 sm:pb-8 text-center">
            <h1 class="text-3xl sm:text-4xl font-bold mb-3 sm:mb-4 tracking-tight text-gray-900 dark:text-white">
                Bảng Xếp Hạng Điểm Tích Lũy
            </h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto text-sm sm:text-base leading-relaxed px-2">
                Vinh danh những người dùng tích cực nhất trong cộng đồng dựa trên điểm số hoạt động và đóng góp thực tế.
            </p>
        </section>

        <!-- Stepped Podium Top 3 -->
        <section class="max-w-6xl mx-auto px-4 sm:px-8 mb-12 sm:mb-16 pt-4 sm:pt-6">
            <div class="flex flex-col md:flex-row items-center md:items-end justify-center gap-10 md:gap-0">
                <!-- RANK 2: BẠC (SILVER) - Khung Rank 2 -->
                <div class="w-full max-w-sm md:max-w-none md:w-1/3 order-2 md:order-1">
                    <div class="bg-white dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 text-center shadow-sm relative z-10 transform translate-y-0 md:translate-y-8">
                        <div class="absolute -top-8 left-1/2 -translate-x-1/2 w-12 h-12 text-white rounded-full flex items-center justify-center font-bold border-4 border-white dark:border-neutral-900 shadow-md z-30"
                             style="background-color: #C0C0C0;">
                            2
                        </div>
                        <div class="mt-10 mb-8 flex justify-center">
                            <div class="avatar-badge-box" style="--avatar-size: 74px;">
                                <div class="avatar-circle shadow-sm border-2 border-neutral-300 dark:border-neutral-700">
                                    @if($rank2 && $rank2->avatar_url)
                                        <img src="{{ $rank2->avatar_url }}" referrerpolicy="no-referrer" alt="{{ $rank2->name }}" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')">
                                        <span class="hidden flex items-center justify-center w-full h-full font-bold text-2xl">{{ $rank2->initialLetter() }}</span>
                                    @elseif($rank2)
                                        <span class="font-bold text-2xl">{{ $rank2->initialLetter() }}</span>
                                    @else
                                        <iconify-icon icon="ph:user-bold" class="text-4xl text-gray-400"></iconify-icon>
                                    @endif
                                </div>
                                <!-- Khung Lottie Rank 2 (ở giữa) -->
                                <div class="avatar-badge-lottie" id="lottie-rank-2"></div>
                            </div>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white truncate mb-3 relative z-30 pt-1" title="{{ $rank2->name ?? 'Minh Anh' }}">
                            {{ $rank2->name ?? 'Minh Anh' }}
                        </h3>
                        <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg py-2 px-4 inline-block font-mono text-sm font-bold text-gray-600 dark:text-gray-300">
                            {{ $rank2 ? number_format($rank2->total_exp) : '2,850' }} XP
                        </div>
                    </div>
                </div>

                <!-- RANK 1: VÀNG (GOLD) - Khung Rank 3 Kim Cương (Cao nhất) -->
                <div class="w-full max-w-sm md:max-w-none md:w-1/3 order-1 md:order-2 px-0 md:px-2">
                    <div class="bg-white dark:bg-neutral-900 border-2 border-blue-600 rounded-2xl p-6 sm:p-8 text-center shadow-xl relative z-20">
                        <div class="absolute -top-10 left-1/2 -translate-x-1/2 w-16 h-16 text-white rounded-full flex items-center justify-center font-extrabold text-2xl border-4 border-white dark:border-neutral-900 shadow-lg z-30"
                             style="background-color: #FFD700;">
                            1
                        </div>
                        <div class="mt-12 mb-10 flex justify-center">
                            <div class="avatar-badge-box" style="--avatar-size: 84px;">
                                <div class="avatar-circle shadow-md border-2 border-amber-400">
                                    @if($rank1 && $rank1->avatar_url)
                                        <img src="{{ $rank1->avatar_url }}" referrerpolicy="no-referrer" alt="{{ $rank1->name }}" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')">
                                        <span class="hidden flex items-center justify-center w-full h-full font-bold text-3xl">{{ $rank1->initialLetter() }}</span>
                                    @elseif($rank1)
                                        <span class="font-bold text-3xl">{{ $rank1->initialLetter() }}</span>
                                    @else
                                        <iconify-icon icon="ph:crown-bold" class="text-5xl text-blue-600"></iconify-icon>
                                    @endif
                                </div>
                                <!-- Khung Lottie Rank 3 Kim Cương (Cao nhất dành cho Top 1) -->
                                <div class="avatar-badge-lottie" id="lottie-rank-1"></div>
                            </div>
                        </div>
                        <h3 class="font-bold text-2xl text-gray-900 dark:text-white truncate mb-4 relative z-30 pt-1" title="{{ $rank1->name ?? 'Nguyễn Hữu Đạt' }}">
                            {{ $rank1->name ?? 'Nguyễn Hữu Đạt' }}
                        </h3>
                        <div class="bg-blue-600 rounded-xl py-3 px-6 inline-block font-mono text-xl font-bold text-white shadow-sm">
                            {{ $rank1 ? number_format($rank1->total_exp) : '3,420' }} XP
                        </div>
                    </div>
                </div>

                <!-- RANK 3: ĐỒNG (BRONZE) - Khung Rank 1 Sắt (Thấp nhất) -->
                <div class="w-full max-w-sm md:max-w-none md:w-1/3 order-3 md:order-3">
                    <div class="bg-white dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 rounded-2xl p-6 text-center shadow-sm relative z-10 transform translate-y-0 md:translate-y-8">
                        <div class="absolute -top-8 left-1/2 -translate-x-1/2 w-12 h-12 text-white rounded-full flex items-center justify-center font-bold border-4 border-white dark:border-neutral-900 shadow-md z-30"
                             style="background-color: #CD7F32;">
                            3
                        </div>
                        <div class="mt-10 mb-8 flex justify-center">
                            <div class="avatar-badge-box" style="--avatar-size: 74px;">
                                <div class="avatar-circle shadow-sm border-2 border-amber-700/50">
                                    @if($rank3 && $rank3->avatar_url)
                                        <img src="{{ $rank3->avatar_url }}" referrerpolicy="no-referrer" alt="{{ $rank3->name }}" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')">
                                        <span class="hidden flex items-center justify-center w-full h-full font-bold text-2xl">{{ $rank3->initialLetter() }}</span>
                                    @elseif($rank3)
                                        <span class="font-bold text-2xl">{{ $rank3->initialLetter() }}</span>
                                    @else
                                        <iconify-icon icon="ph:user-bold" class="text-4xl text-gray-400"></iconify-icon>
                                    @endif
                                </div>
                                <!-- Khung Lottie Rank 1 Sắt (Thấp nhất dành cho Top 3) -->
                                <div class="avatar-badge-lottie" id="lottie-rank-3"></div>
                            </div>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white truncate mb-3 relative z-30 pt-1" title="{{ $rank3->name ?? 'Lê Thị Mai' }}">
                            {{ $rank3->name ?? 'Lê Thị Mai' }}
                        </h3>
                        <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg py-2 px-4 inline-block font-mono text-sm font-bold text-gray-600 dark:text-gray-300">
                            {{ $rank3 ? number_format($rank3->total_exp) : '2,610' }} XP
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Danh sách Top 10 -->
        <section class="max-w-4xl mx-auto px-4 sm:px-8" x-data="{ expanded: false }">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100 dark:border-neutral-800 gap-2">
                <h2 class="font-bold text-lg sm:text-xl text-gray-900 dark:text-white shrink-0">
                    Danh sách Top 10
                </h2>
                <div class="flex gap-1 sm:gap-2">
                    <a href="{{ route('leaderboard.index', ['timeframe' => 'week']) }}"
                       class="text-[11px] sm:text-xs font-bold uppercase tracking-wider sm:tracking-widest px-2.5 sm:px-3 py-1 transition-colors {{ $timeframe === 'week' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-400 hover:text-black dark:hover:text-white' }}">
                        Tuần này
                    </a>
                    <a href="{{ route('leaderboard.index', ['timeframe' => 'month']) }}"
                       class="text-[11px] sm:text-xs font-bold uppercase tracking-wider sm:tracking-widest px-2.5 sm:px-3 py-1 transition-colors {{ $timeframe === 'month' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-400 hover:text-black dark:hover:text-white' }}">
                        Tháng này
                    </a>
                    <a href="{{ route('leaderboard.index', ['timeframe' => 'all']) }}"
                       class="text-[11px] sm:text-xs font-bold uppercase tracking-wider sm:tracking-widest px-2.5 sm:px-3 py-1 transition-colors {{ $timeframe === 'all' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-400 hover:text-black dark:hover:text-white' }}">
                        Tất cả
                    </a>
                </div>
            </div>

            <div class="space-y-2">
                @if($remaining->isNotEmpty())
                    @foreach($remaining as $index => $student)
                        @php
                            $rankNum = $index + 4;
                            $formattedRank = str_pad((string) $rankNum, 2, '0', STR_PAD_LEFT);
                        @endphp
                        <div x-show="expanded || {{ $index < 2 ? 'true' : 'false' }}"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="flex items-center p-3.5 sm:p-4 bg-white dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 rounded-xl hover:shadow-sm transition-shadow group">
                            <div class="w-8 sm:w-10 font-bold text-gray-400 text-sm sm:text-base">{{ $formattedRank }}</div>
                            <div class="flex-1 flex items-center gap-2.5 sm:gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center overflow-hidden shrink-0 shadow-xs select-none">
                                    @if($student->avatar_url)
                                        <img src="{{ $student->avatar_url }}" referrerpolicy="no-referrer" alt="{{ $student->name }}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')">
                                        <span class="hidden flex items-center justify-center w-full h-full font-bold text-xs">{{ $student->initialLetter() }}</span>
                                    @else
                                        <span class="font-bold text-xs">{{ $student->initialLetter() }}</span>
                                    @endif
                                </div>
                                <div class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $student->name }}
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-xs sm:text-sm font-mono font-bold text-gray-900 dark:text-white">{{ number_format($student->total_exp) }} XP</div>
                                <div class="text-[10px] text-emerald-600 font-bold uppercase">+50</div>
                            </div>
                        </div>
                    @endforeach

                    @if($remaining->count() > 2)
                        <div class="text-center mt-8">
                            <button id="btn-load-more" type="button" @click="expanded = !expanded"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-xs transition-all active:scale-95">
                                <span x-text="expanded ? 'Thu gọn danh sách' : 'Xem thêm danh sách'">Xem thêm danh sách</span>
                                <iconify-icon :icon="expanded ? 'lucide:chevron-up' : 'lucide:chevron-down'" class="text-sm"></iconify-icon>
                            </button>
                        </div>
                    @endif
                @else
                    {{-- Demo placeholder if less than 4 users --}}
                    @php
                        $demoItems = [
                            ['rank' => '04', 'name' => 'Lê Hoàng Học Viên', 'exp' => '2,150', 'diff' => '+120', 'color' => 'text-emerald-600'],
                            ['rank' => '05', 'name' => 'Phạm Minh Anh', 'exp' => '1,980', 'diff' => '+80', 'color' => 'text-emerald-600'],
                            ['rank' => '06', 'name' => 'Nguyễn Ngọc Hà', 'exp' => '1,840', 'diff' => '-15', 'color' => 'text-red-500'],
                            ['rank' => '07', 'name' => 'Trần Văn Nam', 'exp' => '1,720', 'diff' => '+45', 'color' => 'text-emerald-600'],
                            ['rank' => '08', 'name' => 'Hoàng Thùy Linh', 'exp' => '1,650', 'diff' => '+10', 'color' => 'text-emerald-600'],
                            ['rank' => '09', 'name' => 'Đỗ Mạnh Cường', 'exp' => '1,590', 'diff' => '0', 'color' => 'text-gray-400'],
                            ['rank' => '10', 'name' => 'Bùi Phương Thảo', 'exp' => '1,520', 'diff' => '+30', 'color' => 'text-emerald-600'],
                        ];
                    @endphp

                    @foreach($demoItems as $index => $item)
                        <div x-show="expanded || {{ $index < 2 ? 'true' : 'false' }}"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="flex items-center p-3.5 sm:p-4 bg-white dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800 rounded-xl hover:shadow-sm transition-shadow group">
                            <div class="w-8 sm:w-10 font-bold text-gray-400 text-sm sm:text-base">{{ $item['rank'] }}</div>
                            <div class="flex-1 flex items-center gap-2.5 sm:gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center overflow-hidden shrink-0 shadow-xs select-none">
                                    <span class="text-xs font-bold">{{ mb_substr($item['name'], 0, 1) }}</span>
                                </div>
                                <div class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $item['name'] }}
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-xs sm:text-sm font-mono font-bold text-gray-900 dark:text-white">{{ $item['exp'] }} XP</div>
                                <div class="text-[10px] {{ $item['color'] }} font-bold uppercase">{{ $item['diff'] }}</div>
                            </div>
                        </div>
                    @endforeach

                    <div class="text-center mt-8">
                        <button id="btn-load-more" type="button" @click="expanded = !expanded"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-xs transition-all active:scale-95">
                            <span x-text="expanded ? 'Thu gọn danh sách' : 'Xem thêm danh sách'">Xem thêm danh sách</span>
                            <iconify-icon :icon="expanded ? 'lucide:chevron-up' : 'lucide:chevron-down'" class="text-sm"></iconify-icon>
                        </button>
                    </div>
                @endif
            </div>
        </section>
    </main>
</div>

<!-- Lottie Animations initialization for Top 3 Ranking Avatars only -->
<script>
(function() {
    function initLeaderboardFrames() {
        if (typeof lottie === 'undefined') {
            setTimeout(initLeaderboardFrames, 100);
            return;
        }

        // Top 1: Rank 3 Kim Cương (Cao nhất)
        // Top 2: Rank 2 (ở giữa)
        // Top 3: Rank 1 Sắt (Thấp nhất trong top 3)
        const frames = [
            { id: 'lottie-rank-1', path: '{{ asset('animations/ranks/rank3-diamond.json') }}' },
            { id: 'lottie-rank-2', path: '{{ asset('animations/ranks/rank2-gold.json') }}' },
            { id: 'lottie-rank-3', path: '{{ asset('animations/ranks/rank1-iron.json') }}' },
        ];

        frames.forEach(function(item) {
            const container = document.getElementById(item.id);
            if (container && !container.hasChildNodes()) {
                lottie.loadAnimation({
                    container: container,
                    renderer: 'svg',
                    loop: true,
                    autoplay: true,
                    path: item.path
                });
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initLeaderboardFrames);
    } else {
        initLeaderboardFrames();
    }
})();
</script>
@endsection
