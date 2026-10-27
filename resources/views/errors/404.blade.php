<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 · Không tìm thấy trang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full items-center justify-center bg-slate-950 p-5 text-slate-800">
    <main class="relative w-full max-w-3xl overflow-hidden rounded-[2rem] bg-white px-6 py-12 text-center shadow-2xl shadow-slate-950/30 sm:px-14 sm:py-16">
        <div class="absolute inset-x-0 top-0 h-2 bg-gradient-to-r from-indigo-600 via-violet-500 to-cyan-400"></div>
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-indigo-50 text-indigo-600 ring-8 ring-indigo-50/60">
            <svg class="h-9 w-9" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.172 16.172a4 4 0 0 1 5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path></svg>
        </div>
        <div class="mt-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Lạc mất đường dẫn?</p>
            <h1 class="mt-3 text-6xl font-bold tracking-tight text-slate-900 sm:text-7xl">404</h1>
            <h2 class="mt-3 text-xl font-semibold text-slate-800">Trang không tồn tại</h2>
            <p class="mx-auto mt-3 max-w-lg text-sm leading-6 text-slate-500">
                Trang bạn đang tìm kiếm có thể đã bị xóa hoặc đường dẫn không chính xác.
            </p>
        </div>
        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Quay lại trang trước
            </a>
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700">
                Về trang chủ
            </a>
        </div>
    </main>
</body>
</html>
