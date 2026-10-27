<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 · Không có quyền truy cập</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full items-center justify-center bg-slate-950 p-5 text-slate-800">
    <main class="relative w-full max-w-3xl overflow-hidden rounded-[2rem] bg-white px-6 py-12 text-center shadow-2xl shadow-slate-950/30 sm:px-14 sm:py-16">
        <div class="absolute inset-x-0 top-0 h-2 bg-gradient-to-r from-rose-500 via-orange-400 to-amber-300"></div>
        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-rose-50 text-rose-600 ring-8 ring-rose-50/60">
            <svg class="h-9 w-9" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2Zm10-10V7a4 4 0 0 0-8 0v4h8Z"></path></svg>
        </div>
        <div class="mt-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-rose-600">Không thể tiếp tục</p>
            <h1 class="mt-3 text-6xl font-bold tracking-tight text-slate-900 sm:text-7xl">403</h1>
            <h2 class="mt-3 text-xl font-semibold text-slate-800">Truy cập bị từ chối</h2>
            <p class="mx-auto mt-3 max-w-lg text-sm leading-6 text-slate-500">
                {{ $exception->getMessage() ?: 'Bạn không có quyền truy cập vào chức năng hoặc trang này.' }}
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
