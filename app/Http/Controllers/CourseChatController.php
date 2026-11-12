<?php

namespace App\Http\Controllers;

use App\Services\CourseAdvisor;
use App\Services\GeminiException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseChatController extends Controller
{
    /**
     * Nhận câu hỏi từ khung chat và trả lời bằng AI dựa trên danh sách khóa học.
     */
    public function __invoke(Request $request, CourseAdvisor $advisor): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'history' => ['nullable', 'array', 'max:20'],
            'history.*.role' => ['required', 'string', 'in:user,assistant'],
            'history.*.content' => ['required', 'string', 'max:4000'],
        ], [
            'message.required' => 'Bạn hãy nhập câu hỏi.',
            'message.max' => 'Câu hỏi tối đa 500 ký tự.',
        ]);

        if (! $advisor->isAvailable()) {
            return response()->json([
                'message' => 'Chat AI hiện chưa được bật. Bạn có thể xem các khóa học ở trang Khóa học nhé.',
            ], 503);
        }

        try {
            $reply = $advisor->reply(trim($validated['message']), $validated['history'] ?? []);
        } catch (GeminiException $exception) {
            Log::warning('Chat AI tư vấn khóa học lỗi: '.$exception->getMessage());

            return response()->json([
                'message' => $exception->isRateLimited()
                    ? 'AI đang quá tải hoặc đã hết lượt miễn phí hôm nay. Bạn thử lại sau ít phút nhé.'
                    : 'AI đang gặp sự cố, bạn thử lại sau ít phút nhé.',
            ], 503);
        }

        return response()->json(['reply' => $reply]);
    }
}
