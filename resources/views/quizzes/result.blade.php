<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả Quiz</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full text-center border-t-4 border-emerald-500">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Nộp bài thành công!</h1>
        <p class="text-gray-500 mb-6">Dưới đây là điểm số của bạn cho bài kiểm tra này.</p>
        
        <div class="bg-gray-50 rounded-xl p-6 mb-8 border border-gray-100">
            <div class="text-sm text-gray-500 uppercase tracking-wider font-semibold mb-1">Điểm số của bạn</div>
            <div class="flex items-baseline justify-center space-x-2">
                <span class="text-5xl font-extrabold text-emerald-600">{{ $attempt->score ?? 0 }}</span>
                <span class="text-xl font-medium text-gray-400">/ {{ $attempt->total_questions ?? 0 }}</span>
            </div>
        </div>

        <!-- Nút quay lại (Tạm trỏ về trang chủ) -->
        <a href="/" class="block w-full bg-gray-800 text-white font-semibold py-3 px-4 rounded-xl hover:bg-gray-900 transition shadow-md">
            Quay về trang chủ
        </a>
    </div>

</body>
</html>