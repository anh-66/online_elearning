<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Không có quyền truy cập</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center bg-gray-50 text-gray-800 p-6">
    <div class="max-w-md w-full text-center space-y-6">
        <div class="w-20 h-20 mx-auto rounded-3xl bg-rose-100 text-rose-600 flex items-center justify-center shadow-lg shadow-rose-200">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">403</h1>
            <h2 class="text-xl font-bold text-gray-800 mt-2">Truy cập bị từ chối</h2>
            <p class="text-sm text-gray-600 mt-2">
                {{ $exception->getMessage() ?: 'Bạn không có quyền truy cập vào chức năng hoặc trang này.' }}
            </p>
        </div>
        <div class="pt-2">
            <a href="{{ url('/') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm shadow-md shadow-indigo-200 transition">
                Về trang chủ
            </a>
        </div>
    </div>
</body>
</html>
