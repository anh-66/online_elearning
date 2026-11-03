@props(['title' => 'Tài khoản'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @include('partials.theme-script')

        <title>{{ $title }} · {{ config('app.name', 'EduLearn') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen w-full bg-white font-sans text-neutral-900 antialiased selection:bg-[#019D91] selection:text-white">
        <!-- Full-Screen Split View Layout (50% Black Left / 50% White Right) -->
        <main class="min-h-screen w-full flex flex-col lg:flex-row">
            {{-- Left Column: Solid Black Hero & Feature Checklist & Stats --}}
            <aside class="w-full lg:w-1/2 min-h-screen bg-black text-white flex flex-col justify-between p-8 sm:p-12 lg:p-16 xl:p-20">
                <!-- Top Brand Logo + Nút Quay lại trang chủ -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3 text-white group" aria-label="{{ config('app.name', 'EduLearn') }}">
                        <svg class="h-8 w-8 text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                        <span class="text-2xl font-bold tracking-tight">EduLearn</span>
                    </a>

                    <!-- Nút Quay lại trang chủ theo mẫu Uiverse (text fades on hover) -->
                    <a href="{{ route('home') }}"
                       class="relative inline-flex items-center justify-center h-11 w-44 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/20 text-xs sm:text-sm font-semibold text-white overflow-hidden group shadow-sm transition-all active:scale-[0.98]">
                        <div class="absolute left-1 top-[3px] bottom-[3px] w-9 rounded-xl bg-[#019D91] flex items-center justify-center text-white transition-all duration-300 ease-out group-hover:w-[calc(100%-8px)] z-10 shadow-sm">
                            <svg class="w-4 h-4 text-white transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </div>
                        <span class="pl-8 transition-all duration-200 group-hover:opacity-0 group-hover:scale-90 select-none text-white">Quay lại trang chủ</span>
                    </a>
                </div>

                <!-- Center: Heading & Core Features Checklist -->
                <div class="py-6 my-auto max-w-xl">
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-white leading-tight">
                        @if(request()->routeIs('login'))
                            Chào mừng bạn trở lại
                        @else
                            Tạo tài khoản miễn phí
                        @endif
                    </h1>
                    <p class="mt-3 text-sm sm:text-base text-neutral-400 font-normal leading-relaxed">
                        @if(request()->routeIs('login'))
                            Đăng nhập để tiếp tục lộ trình học tập và theo sát tiến độ cùng chuyên gia.
                        @else
                            Khám phá các tính năng học tập chuyên sâu dành cho học viên và sinh viên.
                        @endif
                    </p>

                    <p class="mt-7 text-sm font-semibold text-neutral-200 flex items-center gap-1.5 cursor-default">
                        <span>Quyền lợi tài khoản thành viên</span>
                        <span class="text-xs">&and;</span>
                    </p>

                    {{-- Checklist items hoàn toàn tiếng Việt --}}
                    <div class="mt-4 space-y-3.5 text-sm text-neutral-300">
                        <div class="flex items-start gap-3">
                            <span class="text-white font-bold text-base mt-0.5 leading-none shrink-0">&#10003;</span>
                            <div>
                                <p class="font-semibold text-white">Theo dõi tiến độ</p>
                                <p class="text-xs sm:text-sm text-neutral-400 mt-0.5">Nắm bắt tiến trình hoàn thành từng bài học.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="text-white font-bold text-base mt-0.5 leading-none shrink-0">&#10003;</span>
                            <div>
                                <p class="font-semibold text-white">Bài kiểm tra Quiz</p>
                                <p class="text-xs sm:text-sm text-neutral-400 mt-0.5">Làm trắc nghiệm củng cố kiến thức và nhận điểm tự động.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="text-white font-bold text-base mt-0.5 leading-none shrink-0">&#10003;</span>
                            <div>
                                <p class="font-semibold text-white">Hỏi đáp bài học</p>
                                <p class="text-xs sm:text-sm text-neutral-400 mt-0.5">Thảo luận trực tiếp và đặt câu hỏi dưới mỗi video.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="text-white font-bold text-base mt-0.5 leading-none shrink-0">&#10003;</span>
                            <div>
                                <p class="font-semibold text-white">Danh sách yêu thích</p>
                                <p class="text-xs sm:text-sm text-neutral-400 mt-0.5">Lưu lại các khóa học quan tâm để xem lại sau.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="text-white font-bold text-base mt-0.5 leading-none shrink-0">&#10003;</span>
                            <div>
                                <p class="font-semibold text-white">Hồ sơ &amp; Avatar</p>
                                <p class="text-xs sm:text-sm text-neutral-400 mt-0.5">Cập nhật thông tin cá nhân và ảnh đại diện chuyên nghiệp.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Thống kê người dùng thực tế -->
                    <div class="mt-8 pt-6 border-t border-white/10 flex items-center justify-center gap-16 sm:gap-24 text-center">
                        <div>
                            <p class="text-4xl sm:text-5xl font-extrabold tracking-tight text-white leading-none">
                                {{ number_format($instructorCount) }}
                            </p>
                            <p class="mt-2 text-sm text-neutral-400 font-medium">
                                Giảng viên
                            </p>
                        </div>

                        <div>
                            <p class="text-4xl sm:text-5xl font-extrabold tracking-tight text-white leading-none">
                                {{ number_format($studentCount) }}
                            </p>
                            <p class="mt-2 text-sm text-neutral-400 font-medium">
                                Bạn học
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer bản quyền nhỏ gọn -->
                <div class="text-xs text-neutral-500 text-center sm:text-left pt-4">
                    &copy; {{ date('Y') }} EduLearn Platform. Nền tảng học trực tuyến chất lượng cao.
                </div>
            </aside>

            {{-- Right Column: Crisp White Form Column --}}
            <section class="w-full lg:w-1/2 min-h-screen bg-white text-neutral-900 flex flex-col justify-between p-8 sm:p-12 lg:p-16 xl:p-20">
                <!-- Top Right Switcher Link (Hoàn toàn tiếng Việt) -->
                <div class="flex items-center justify-end text-sm">
                    @if (request()->routeIs('login') && Route::has('register'))
                        <div class="text-xs sm:text-sm text-neutral-600">
                            Chưa có tài khoản?
                            <a href="{{ route('register') }}" class="font-semibold text-[#019D91] hover:underline ml-1">Đăng ký ngay &rarr;</a>
                        </div>
                    @elseif (request()->routeIs('register'))
                        <div class="text-xs sm:text-sm text-neutral-600">
                            Đã có tài khoản?
                            <a href="{{ route('login') }}" class="font-semibold text-[#019D91] hover:underline ml-1">Đăng nhập ngay &rarr;</a>
                        </div>
                    @else
                        <a href="{{ route('home') }}" class="text-xs sm:text-sm text-neutral-600 hover:text-neutral-900 transition">&larr; Quay lại trang chủ</a>
                    @endif
                </div>

                <!-- Center: Auth Form Container -->
                <div class="w-full max-w-md mx-auto my-auto py-6">
                    {{ $slot }}
                </div>

                <!-- Empty bottom spacing to maintain balance -->
                <div class="hidden lg:block text-xs text-neutral-400 text-center">
                    Hỗ trợ người học: support@edulearn.vn
                </div>
            </section>
        </main>
    </body>
</html>
