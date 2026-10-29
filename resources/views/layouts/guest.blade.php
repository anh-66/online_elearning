@props(['title' => 'Tài khoản'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }} · {{ config('app.name', 'EduLearn') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-full bg-slate-100 font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">
        <!-- Background Subtle Pattern -->
        <div class="fixed inset-0 pointer-events-none bg-[radial-gradient(#cbd5e1_1px,transparent_1px)] [background-size:16px_16px] opacity-40"></div>

        <main class="relative min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-10">
            <!-- Main Card Container: Crisp Modern Style, Subtle Rounded Corners -->
            <div class="w-full max-w-5xl overflow-hidden rounded-lg border border-slate-200/90 bg-white shadow-lg shadow-slate-200/50 grid lg:grid-cols-12 min-h-[620px]">

                <!-- Left Showcase Panel (5 Columns on Desktop) -->
                <aside class="relative hidden lg:flex lg:col-span-5 flex-col justify-between overflow-hidden bg-slate-900 p-8 xl:p-10 text-white">
                    <!-- Background Showcase Image with Dark Modern Overlay -->
                    <img
                        src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80"
                        alt="E-learning banner"
                        class="absolute inset-0 h-full w-full object-cover opacity-20 mix-blend-luminosity filter contrast-125"
                    />
                    <div class="absolute inset-0 bg-gradient-to-b from-indigo-950/85 via-slate-900/95 to-slate-950"></div>

                    <!-- Decorative subtle grid element -->
                    <div class="pointer-events-none absolute -left-24 -top-24 h-72 w-72 rounded-full bg-indigo-500/10 blur-3xl"></div>

                    <!-- Brand / Home Link -->
                    <div class="relative z-10">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 group">
                            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-tr from-indigo-500 to-indigo-600 text-white shadow-sm shadow-indigo-500/30 transition group-hover:scale-105">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </span>
                            <span class="text-xl font-bold tracking-tight text-white">
                                {{ config('app.name', 'EduLearn') }}
                            </span>
                        </a>
                    </div>

                    <!-- Showcase Highlights -->
                    <div class="relative z-10 my-8">
                        <span class="inline-flex items-center gap-1.5 rounded-md border border-indigo-500/30 bg-indigo-500/20 px-2.5 py-1 text-xs font-semibold uppercase tracking-wider text-indigo-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                            Nền tảng học trực tuyến
                        </span>

                        <h2 class="mt-4 text-2xl xl:text-3xl font-bold tracking-tight text-white leading-snug">
                            Nâng tầm kỹ năng.<br>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 via-cyan-200 to-white">
                                Vững bước tương lai.
                            </span>
                        </h2>

                        <p class="mt-3 text-sm text-slate-300 leading-relaxed">
                            Khám phá hàng trăm khóa học thực chiến từ lập trình, thiết kế đến kinh doanh cùng lộ trình bài bản.
                        </p>

                        <!-- Key Feature Bullets -->
                        <div class="mt-6 space-y-3">
                            <div class="flex items-center gap-3 text-xs xl:text-sm text-slate-200">
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-emerald-500/30 bg-emerald-500/20 text-emerald-400">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                </span>
                                <span>Bài giảng chuẩn hóa, thực hành qua dự án</span>
                            </div>
                            <div class="flex items-center gap-3 text-xs xl:text-sm text-slate-200">
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-emerald-500/30 bg-emerald-500/20 text-emerald-400">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                </span>
                                <span>Giảng viên kinh nghiệm & cộng đồng hỗ trợ</span>
                            </div>
                            <div class="flex items-center gap-3 text-xs xl:text-sm text-slate-200">
                                <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-emerald-500/30 bg-emerald-500/20 text-emerald-400">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                </span>
                                <span>Chứng nhận hoàn thành xác thực uy tín</span>
                            </div>
                        </div>
                    </div>

                    <!-- Social Proof / Mini Card -->
                    <div class="relative z-10 pt-4 border-t border-slate-800/80">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center -space-x-2">
                                <img class="h-7 w-7 rounded-full ring-2 ring-slate-900 object-cover" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80" alt="Avatar">
                                <img class="h-7 w-7 rounded-full ring-2 ring-slate-900 object-cover" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80" alt="Avatar">
                                <img class="h-7 w-7 rounded-full ring-2 ring-slate-900 object-cover" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80" alt="Avatar">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-slate-800 ring-2 ring-slate-900 text-[10px] font-semibold text-slate-300">+10k</span>
                            </div>
                            <div class="flex items-center gap-1 text-amber-400 text-xs font-semibold">
                                <span>★ 4.9/5</span>
                                <span class="text-slate-400 font-normal">đánh giá</span>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Right Form Panel (7 Columns on Desktop) -->
                <section class="lg:col-span-7 flex flex-col justify-center px-6 py-8 sm:px-10 sm:py-10 lg:px-12 bg-white">
                    <!-- Mobile Brand Header -->
                    <div class="mb-6 lg:hidden flex items-center justify-between">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 group">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm">
                                <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </span>
                            <span class="text-lg font-bold text-slate-900">{{ config('app.name', 'EduLearn') }}</span>
                        </a>
                        <a href="{{ url('/') }}" class="text-xs font-medium text-slate-500 hover:text-indigo-600 transition">
                            Về trang chủ →
                        </a>
                    </div>

                    <!-- Slot Content -->
                    <div class="w-full max-w-md mx-auto">
                        {{ $slot }}
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
