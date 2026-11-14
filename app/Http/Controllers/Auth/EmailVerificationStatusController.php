<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\EmailOtp;
use App\Services\BrevoDeliveryTracker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * Trang nhập mã hỏi định kỳ: mail chứa mã đã thật sự tới hộp thư chưa (đọc từ Brevo).
 * Chỉ trả về mail của chính người đang đăng nhập.
 */
class EmailVerificationStatusController extends Controller
{
    public function __invoke(Request $request, BrevoDeliveryTracker $tracker): JsonResponse
    {
        $log = EmailLog::query()
            ->where('user_id', $request->user()->getKey())
            ->where('purpose', EmailOtp::PURPOSE_VERIFY_EMAIL)
            ->latest('id')
            ->first();

        if ($log === null) {
            return response()->json(['state' => 'none', 'final' => true, 'message' => null]);
        }

        if ($log->isAwaitingDelivery() && ($log->checked_at === null || $log->checked_at->lt(now()->subSeconds(4)))) {
            try {
                $tracker->sync([$log]);
            } catch (Throwable) {
                // Không đọc được thì giữ trạng thái cũ, lần hỏi sau thử lại.
            }
        }

        return response()->json($this->describe($log));
    }

    /**
     * @return array{state: string, final: bool, message: ?string}
     */
    private function describe(EmailLog $log): array
    {
        $tooOld = $log->created_at->lt(now()->subMinutes(15));

        return match (true) {
            $log->onlyLogged() => ['state' => 'logged', 'final' => true, 'message' => null],
            $log->isDelivered() => [
                'state' => 'delivered',
                'final' => true,
                'message' => 'Mail chứa mã đã tới hộp thư của bạn. Không thấy thì xem mục Spam / Quảng cáo nhé.',
            ],
            $log->status === EmailLog::STATUS_REJECTED && $log->isSenderProblem() => [
                'state' => 'rejected',
                'final' => true,
                'message' => 'Hệ thống gửi mail đang gặp lỗi cấu hình nên mail chưa tới được. Bạn thử lại sau ít phút hoặc báo quản trị viên nhé.',
            ],
            $log->status === EmailLog::STATUS_REJECTED => [
                'state' => 'rejected',
                'final' => true,
                'message' => 'Không gửi được mail tới địa chỉ này. Kiểm tra lại email đã nhập, nếu sai thì đăng xuất và đăng ký lại bằng email đúng.',
            ],
            $log->status === EmailLog::STATUS_FAILED => [
                'state' => 'failed',
                'final' => true,
                'message' => 'Hệ thống chưa gửi được mail. Bạn đợi một chút rồi bấm "Gửi lại mã" nhé.',
            ],
            $log->status === EmailLog::STATUS_DEFERRED => [
                'state' => 'deferred',
                'final' => $tooOld,
                'message' => 'Hộp thư của bạn đang tạm từ chối nhận, hệ thống đang gửi lại. Đợi thêm ít phút nhé.',
            ],
            ! $log->isTrackable() => ['state' => 'sent', 'final' => true, 'message' => null],
            default => [
                'state' => 'sending',
                'final' => $tooOld,
                'message' => 'Đang chờ hộp thư của bạn nhận mail...',
            ],
        };
    }
}
