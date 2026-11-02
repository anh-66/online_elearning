@props(['title' => 'Tài khoản'])

@php
    // Nội dung panel bên trái thay đổi theo từng trang xác thực
    [$asideHeading, $asideText] = match (true) {
        request()->routeIs('login') => ['Chào mừng bạn quay lại', 'Đăng nhập để tiếp tục các khóa học đang dang dở và theo dõi tiến độ của bạn.'],
        request()->routeIs('password.*') => ['Bảo mật tài khoản', 'Khôi phục quyền truy cập chỉ trong vài bước. Liên kết đặt lại mật khẩu sẽ được gửi qua email.'],
        request()->routeIs('verification.*') => ['Chỉ còn một bước nữa', 'Xác minh email để bảo vệ tài khoản và nhận thông báo về khóa học.'],
        default => ['Tạo tài khoản miễn phí', 'Khám phá những công cụ giúp bạn học tập chủ động và kết nối với cộng đồng.'],
    };
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @include('partials.theme-script')

        <title>{{ $title }} · {{ config('app.name', 'EduLearn') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700|figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-full bg-white font-sans text-neutral-900 dark:bg-neutral-950 dark:text-neutral-100 antialiased selection:bg-brand-700 selection:text-white">
        <main class="flex min-h-screen items-center justify-center sm:p-5 lg:p-8">
            <div class="grid w-full max-w-6xl overflow-hidden border-y border-neutral-200 bg-white shadow-xl shadow-neutral-200/50 dark:border-neutral-800 dark:bg-neutral-950 dark:shadow-none sm:border-x lg:min-h-[680px] lg:grid-cols-2">
                <aside class="flex flex-col justify-between border-b border-brand-100 bg-gradient-to-br from-brand-50 via-white to-brand-100/60 px-7 py-8 text-neutral-900 dark:border-neutral-800 dark:from-neutral-900 dark:via-neutral-900 dark:to-brand-950/50 dark:text-white lg:border-b-0 lg:border-r sm:px-10 sm:py-10 lg:px-12 lg:py-12">
                    <a href="{{ route('home') }}" class="inline-flex w-fit items-center gap-3 text-neutral-900 dark:text-white" aria-label="{{ config('app.name', 'EduLearn') }} - Trang chủ">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-700 text-white">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.25v13m0-13C10.83 5.48 9.25 5 7.5 5S4.17 5.48 3 6.25v13C4.17 18.48 5.75 18 7.5 18s3.33.48 4.5 1.25Zm0 0C13.17 5.48 14.75 5 16.5 5s3.33.48 4.5 1.25v13C19.83 18.48 18.25 18 16.5 18s-3.33.48-4.5 1.25Z" />
                            </svg>
                        </span>
                        <span class="text-lg font-semibold tracking-tight">{{ config('app.name', 'EduLearn') }}</span>
                    </a>

                    <div class="py-10 lg:py-12">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-brand-700 dark:text-brand-400">Nền tảng học trực tuyến</p>
                        <h1 class="mt-4 max-w-lg text-3xl font-bold leading-tight tracking-tight text-neutral-900 dark:text-white sm:text-4xl">
                            {{ $asideHeading }}
                        </h1>
                        <p class="mt-3 max-w-lg text-sm leading-6 text-neutral-600 dark:text-neutral-300 sm:text-base">
                            {{ $asideText }}
                        </p>

                        <div class="mt-7 space-y-4">
                            <div class="flex items-start gap-3 text-sm text-neutral-700 dark:text-neutral-200">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center text-brand-700 dark:text-brand-400">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" /></svg>
                                </span>
                                <span><strong class="font-semibold text-neutral-900 dark:text-white">Theo dõi tiến độ</strong><span class="mt-1 block text-xs leading-5 text-neutral-500 dark:text-neutral-400">Nắm bắt tiến trình hoàn thành khóa học.</span></span>
                            </div>
                            <div class="flex items-start gap-3 text-sm text-neutral-700 dark:text-neutral-200">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center text-brand-700 dark:text-brand-400">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" /></svg>
                                </span>
                                <span><strong class="font-semibold text-neutral-900 dark:text-white">Bài kiểm tra Quiz</strong><span class="mt-1 block text-xs leading-5 text-neutral-500 dark:text-neutral-400">Luyện tập và nhận kết quả tự động.</span></span>
                            </div>
                            <div class="flex items-start gap-3 text-sm text-neutral-700 dark:text-neutral-200">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center text-brand-700 dark:text-brand-400">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" /></svg>
                                </span>
                                <span><strong class="font-semibold text-neutral-900 dark:text-white">Hỏi đáp bài học</strong><span class="mt-1 block text-xs leading-5 text-neutral-500 dark:text-neutral-400">Trao đổi và đặt câu hỏi dưới mỗi bài.</span></span>
                            </div>
                            <div class="flex items-start gap-3 text-sm text-neutral-700 dark:text-neutral-200">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center text-brand-700 dark:text-brand-400">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" /></svg>
                                </span>
                                <span><strong class="font-semibold text-neutral-900 dark:text-white">Danh sách yêu thích</strong><span class="mt-1 block text-xs leading-5 text-neutral-500 dark:text-neutral-400">Lưu lại khóa học bạn quan tâm.</span></span>
                            </div>
                            <div class="flex items-start gap-3 text-sm text-neutral-700 dark:text-neutral-200">
                                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center text-brand-700 dark:text-brand-400">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" /></svg>
                                </span>
                                <span><strong class="font-semibold text-neutral-900 dark:text-white">Hồ sơ cá nhân</strong><span class="mt-1 block text-xs leading-5 text-neutral-500 dark:text-neutral-400">Cập nhật thông tin và ảnh đại diện.</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-5 border-t border-brand-200/70 dark:border-neutral-800 pt-6 sm:gap-8 sm:pt-7">
                        <div>
                            <p class="text-3xl font-bold tracking-tight text-neutral-900 dark:text-white sm:text-4xl">{{ number_format($instructorCount, 0, ',', '.') }}</p>
                            <p class="mt-1 text-xs text-neutral-600 dark:text-neutral-300 sm:text-sm">Giảng viên</p>
                        </div>
                        <div>
                            <p class="text-3xl font-bold tracking-tight text-neutral-900 dark:text-white sm:text-4xl">{{ number_format($studentCount, 0, ',', '.') }}</p>
                            <p class="mt-1 text-xs text-neutral-600 dark:text-neutral-300 sm:text-sm">Bạn học</p>
                        </div>
                    </div>
                </aside>

                <section class="flex flex-col px-6 py-7 sm:px-10 sm:py-9 lg:px-14 lg:py-10">
                    <div class="flex min-h-8 items-center justify-end gap-3 text-right text-xs sm:text-sm">
                        @if (request()->routeIs('login') && Route::has('register'))
                            <p class="text-neutral-500 dark:text-neutral-400">Chưa có tài khoản?
                                <a href="{{ route('register') }}" class="ml-1 font-semibold text-neutral-900 transition hover:text-brand-700 dark:text-white dark:hover:text-brand-400">Đăng ký <span aria-hidden="true">→</span></a>
                            </p>
                        @elseif (request()->routeIs('register'))
                            <p class="text-neutral-500 dark:text-neutral-400">Đã có tài khoản?
                                <a href="{{ route('login') }}" class="ml-1 font-semibold text-neutral-900 transition hover:text-brand-700 dark:text-white dark:hover:text-brand-400">Đăng nhập <span aria-hidden="true">→</span></a>
                            </p>
                        @endif

                        <x-theme-toggle />
                    </div>

                    <div class="mx-auto my-auto w-full max-w-sm py-8">
                        {{ $slot }}
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
