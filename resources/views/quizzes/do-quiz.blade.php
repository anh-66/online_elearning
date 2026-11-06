<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Làm bài Quiz</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen pb-12">

    <div class="max-w-3xl mx-auto pt-10 px-4">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Bài kiểm tra trắc nghiệm</h1>
            <span class="bg-indigo-100 text-indigo-800 text-sm font-semibold px-3 py-1 rounded-full">Tính điểm tự động</span>
        </div>

        <!-- Form nộp bài trỏ về Route quiz.submit -->
        <form action="{{ route('quiz.submit', ['id' => $quiz->id ?? 1]) }}" method="POST">
            @csrf
            
            <!-- Khung Câu hỏi 1 (Mẫu) -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Câu 1: Laravel là gì?</h3>
                <div class="space-y-3">
                    <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="answers[1]" value="1" class="w-5 h-5 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-gray-700">Một Framework của PHP</span>
                    </label>
                    <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="answers[1]" value="2" class="w-5 h-5 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-gray-700">Một ngôn ngữ lập trình mới</span>
                    </label>
                </div>
            </div>

            <!-- Khung Câu hỏi 2 (Mẫu) -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Câu 2: MVC viết tắt của từ gì?</h3>
                <div class="space-y-3">
                    <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="answers[2]" value="3" class="w-5 h-5 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-gray-700">Model View Controller</span>
                    </label>
                    <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="answers[2]" value="4" class="w-5 h-5 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-gray-700">Main Visual Component</span>
                    </label>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="bg-emerald-600 text-white font-bold text-lg py-3 px-10 rounded-xl hover:bg-emerald-700 transition shadow-lg">
                    Nộp bài & Chấm điểm
                </button>
            </div>
        </form>
    </div>

</body>
</html>