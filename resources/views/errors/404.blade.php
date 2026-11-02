<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 · Không tìm thấy trang · {{ config('app.name', 'EduLearn') }}</title>
    @include('partials.theme-script')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative flex min-h-full items-center justify-center overflow-hidden bg-neutral-100 p-5 font-sans text-neutral-800 dark:bg-neutral-950 dark:text-neutral-200 antialiased selection:bg-brand-700 selection:text-white">
    <div class="pointer-events-none fixed inset-0 bg-[radial-gradient(#d4d4d4_1px,transparent_1px)] opacity-40 [background-size:16px_16px] dark:opacity-10"></div>
    <main class="relative w-full max-w-3xl overflow-hidden rounded-lg border border-neutral-200/90 dark:border-neutral-800 bg-white dark:bg-neutral-900 px-6 py-10 text-center shadow-lg shadow-neutral-200/60 dark:shadow-black/20 sm:px-14 sm:py-14">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-brand-800 via-brand-600 to-brand-300"></div>
        <div class="mb-8 flex items-center justify-center gap-2 text-sm font-bold tracking-tight text-neutral-900 dark:text-neutral-100">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-700 text-white">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            </span>
            {{ config('app.name', 'EduLearn') }}
        </div>
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-lg bg-brand-50 dark:bg-brand-500/10 text-brand-700 dark:text-brand-400">
            <svg class="h-9 w-9" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.172 16.172a4 4 0 0 1 5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path></svg>
        </div>
        <div class="mt-7">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-brand-700 dark:text-brand-400">Lạc mất đường dẫn?</p>
            <h1 class="mt-3 text-6xl font-bold tracking-tight text-neutral-900 dark:text-neutral-100 sm:text-7xl">404</h1>
            <h2 class="mt-3 text-xl font-semibold text-neutral-800 dark:text-neutral-100">Trang không tồn tại</h2>
            <p class="mx-auto mt-3 max-w-lg text-sm leading-6 text-neutral-500 dark:text-neutral-400">
                Trang bạn đang tìm kiếm có thể đã bị xóa hoặc đường dẫn không chính xác.
            </p>
        </div>
        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center rounded-md border border-neutral-200 dark:border-neutral-800 px-5 py-3 text-sm font-semibold text-neutral-700 dark:text-neutral-200 transition hover:border-neutral-300 dark:hover:border-neutral-600 hover:bg-neutral-50 dark:hover:bg-neutral-800">
                Quay lại trang trước
            </a>
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-md bg-brand-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800">
                Về trang chủ
            </a>
        </div>
    </main>
</body>
</html>
