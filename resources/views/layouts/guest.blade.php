@props(['title' => 'Tài khoản'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }} · {{ config('app.name', 'E-learning') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 font-sans text-slate-900 antialiased">
        <main class="flex min-h-screen items-center justify-center p-4 sm:p-8">
            <div class="grid w-full max-w-6xl overflow-hidden rounded-[2rem] bg-white shadow-2xl shadow-slate-950/30 lg:min-h-[680px] lg:grid-cols-[0.95fr_1.05fr]">
                <aside class="relative hidden flex-col justify-between overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-700 to-slate-900 p-12 text-white lg:flex">
                    <div class="absolute -right-24 -top-20 h-80 w-80 rounded-full border border-white/10"></div>
                    <div class="absolute -bottom-36 -left-20 h-96 w-96 rounded-full border border-white/10"></div>
                    <div class="absolute right-10 top-32 h-48 w-48 rounded-full bg-cyan-300/10 blur-3xl"></div>

                    <a href="{{ route('home') }}" class="relative inline-flex items-center gap-3 text-lg font-bold tracking-tight text-white">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path d="M3 5.5A2.5 2.5 0 0 1 5.5 3H20v16H5.5A2.5 2.5 0 0 0 3 21.5v-16Z" stroke-width="1.8" stroke-linejoin="round" />
                                <path d="M3 17.5A2.5 2.5 0 0 1 5.5 15H20M8 7h7M8 10h7" stroke-width="1.8" stroke-linecap="round" />
                            </svg>
                        </span>
                        {{ config('app.name', 'E-learning') }}
                    </a>

                    <div class="relative max-w-md">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-indigo-100 ring-1 ring-white/15">
                            <span class="h-1.5 w-1.5 rounded-full bg-cyan-300"></span>
                            Học tập không giới hạn
                        </span>
                        <h1 class="mt-7 text-4xl font-semibold leading-tight tracking-tight xl:text-5xl">
                            Mở rộng kiến thức.<br>
                            <span class="text-cyan-200">Tiến xa hơn.</span>
                        </h1>
                        <p class="mt-5 max-w-sm text-base leading-7 text-indigo-100/85">
                            Khám phá khóa học chất lượng, học theo nhịp độ của bạn và phát triển mỗi ngày.
                        </p>
                    </div>

                    <p class="relative text-sm text-indigo-100/70">Một bước nhỏ hôm nay, một hành trình mới ngày mai.</p>
                </aside>

                <section class="flex items-center justify-center px-6 py-10 sm:px-12 lg:px-14">
                    <div class="w-full max-w-md">
                        <a href="{{ route('home') }}" class="mb-9 inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-indigo-600 lg:hidden">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path d="M3 5.5A2.5 2.5 0 0 1 5.5 3H20v16H5.5A2.5 2.5 0 0 0 3 21.5v-16Z" stroke-width="1.8" stroke-linejoin="round" />
                                    <path d="M3 17.5A2.5 2.5 0 0 1 5.5 15H20" stroke-width="1.8" stroke-linecap="round" />
                                </svg>
                            </span>
                            {{ config('app.name', 'E-learning') }}
                        </a>
                        {{ $slot }}
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
