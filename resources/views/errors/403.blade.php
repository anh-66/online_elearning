<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 · Không có quyền truy cập · {{ config('app.name', 'EduLearn') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative flex min-h-full items-center justify-center overflow-hidden bg-slate-100 p-5 font-sans text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">
    <div class="pointer-events-none fixed inset-0 bg-[radial-gradient(#cbd5e1_1px,transparent_1px)] opacity-40 [background-size:16px_16px]"></div>
    <main class="relative w-full max-w-3xl overflow-hidden rounded-lg border border-slate-200/90 bg-white px-6 py-10 text-center shadow-lg shadow-slate-200/60 sm:px-14 sm:py-14">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-rose-500 via-orange-400 to-amber-300"></div>
        <div class="mb-8 flex items-center justify-center gap-2 text-sm font-bold tracking-tight text-slate-900">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm shadow-indigo-500/25">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            </span>
            {{ config('app.name', 'EduLearn') }}
        </div>
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
            <svg class="h-9 w-9" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Zm10-10V7a4 4 0 0 0-8 0v4h8Z"></path></svg>
        </div>
        <div class="mt-7">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-rose-600">Không thể tiếp tục</p>
            <h1 class="mt-3 text-6xl font-bold tracking-tight text-slate-900 sm:text-7xl">403</h1>
            <h2 class="mt-3 text-xl font-semibold text-slate-800">Truy cập bị từ chối</h2>
            <p class="mx-auto mt-3 max-w-lg text-sm leading-6 text-slate-500">
                {{ $exception->getMessage() ?: 'Bạn không có quyền truy cập vào chức năng hoặc trang này.' }}
            </p>
        </div>
        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center rounded-md border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                Quay lại trang trước
            </a>
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                Về trang chủ
            </a>
        </div>
    </main>
</body>
</html>
