<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\EmailOtp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmailLogController extends Controller
{
    /**
     * Danh sách mail đã gửi: thành công hay thất bại, lỗi gì.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['all', EmailLog::STATUS_SENT, EmailLog::STATUS_FAILED])],
            'purpose' => ['nullable', Rule::in(['all', EmailOtp::PURPOSE_VERIFY_EMAIL, EmailOtp::PURPOSE_RESET_PASSWORD])],
        ]);

        $keyword = $validated['keyword'] ?? null;
        $status = $validated['status'] ?? 'all';
        $purpose = $validated['purpose'] ?? 'all';

        // Tự dọn nhật ký cũ (Render Free không có cron).
        EmailLog::query()->where('created_at', '<', now()->subDays(EmailLog::KEEP_DAYS))->delete();

        $logs = EmailLog::query()
            ->with('user:id,name,email')
            ->when($keyword, function ($query, string $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('to_email', 'like', "%{$keyword}%")
                        ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$keyword}%"));
                });
            })
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($purpose !== 'all', fn ($query) => $query->where('purpose', $purpose))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $since = now()->subDays(7);
        $stats = [
            'today' => EmailLog::query()->where('created_at', '>=', now()->startOfDay())->count(),
            'sent' => EmailLog::query()->where('status', EmailLog::STATUS_SENT)->where('created_at', '>=', $since)->count(),
            'failed' => EmailLog::query()->where('status', EmailLog::STATUS_FAILED)->where('created_at', '>=', $since)->count(),
        ];
        $total = $stats['sent'] + $stats['failed'];
        $stats['success_rate'] = $total > 0 ? (int) round($stats['sent'] * 100 / $total) : null;

        $mailer = (string) config('mail.default');

        return view('admin.email-logs.index', [
            'logs' => $logs,
            'stats' => $stats,
            'keyword' => $keyword,
            'status' => $status,
            'purpose' => $purpose,
            'mailer' => $mailer,
            'onlyLogging' => in_array($mailer, ['log', 'array'], true),
            'timezone' => 'Asia/Ho_Chi_Minh',
        ]);
    }
}
