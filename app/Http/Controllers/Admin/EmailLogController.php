<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Models\EmailOtp;
use App\Services\BrevoDeliveryTracker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class EmailLogController extends Controller
{
    /**
     * Danh sách mail đã gửi: đã tới hộp thư chưa, bị từ chối vì sao.
     */
    public function index(Request $request, BrevoDeliveryTracker $tracker): View
    {
        $validated = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['all', 'problem', ...EmailLog::STATUSES])],
            'purpose' => ['nullable', Rule::in(['all', EmailOtp::PURPOSE_VERIFY_EMAIL, EmailOtp::PURPOSE_RESET_PASSWORD])],
        ]);

        $keyword = $validated['keyword'] ?? null;
        $status = $validated['status'] ?? 'all';
        $purpose = $validated['purpose'] ?? 'all';

        // Tự dọn nhật ký cũ mỗi khi admin mở trang (không cần cron).
        EmailLog::query()->where('created_at', '<', now()->subDays(EmailLog::KEEP_DAYS))->delete();

        // Hỏi Brevo trạng thái thật của vài mail mới nhất còn đang chờ (nhanh, lỗi thì bỏ qua).
        $this->syncQuietly($tracker, 10);

        $logs = EmailLog::query()
            ->with('user:id,name,email')
            ->when($keyword, function ($query, string $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('to_email', 'like', "%{$keyword}%")
                        ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$keyword}%"));
                });
            })
            ->when($status === 'problem', fn ($query) => $query->whereIn('status', [EmailLog::STATUS_FAILED, EmailLog::STATUS_REJECTED]))
            ->when(! in_array($status, ['all', 'problem'], true), fn ($query) => $query->where('status', $status))
            ->when($purpose !== 'all', fn ($query) => $query->where('purpose', $purpose))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $mailer = (string) config('mail.default');

        return view('admin.email-logs.index', [
            'logs' => $logs,
            'stats' => $this->stats(),
            'keyword' => $keyword,
            'status' => $status,
            'purpose' => $purpose,
            'mailer' => $mailer,
            'onlyLogging' => in_array($mailer, ['log', 'array'], true),
            'trackerEnabled' => $tracker->enabled(),
            'trackerProblem' => $tracker->lastProblem(),
            'awaiting' => $tracker->enabled() ? $tracker->pendingQuery(force: true)->count() : 0,
            'timezone' => 'Asia/Ho_Chi_Minh',
        ]);
    }

    /**
     * Nút "Cập nhật trạng thái": hỏi Brevo ngay cho tối đa 50 mail đang chờ.
     */
    public function sync(BrevoDeliveryTracker $tracker): RedirectResponse
    {
        if (! $tracker->enabled()) {
            return back()->with('warning', 'Chưa có BREVO_API_KEY nên không hỏi được Brevo.');
        }

        $changed = $this->syncQuietly($tracker, 50, force: true);

        if ($problem = $tracker->lastProblem()) {
            return back()->with('warning', $problem);
        }

        return back()->with('success', $changed > 0
            ? "Đã cập nhật trạng thái của {$changed} mail."
            : 'Đã hỏi Brevo, chưa có mail nào đổi trạng thái.');
    }

    private function syncQuietly(BrevoDeliveryTracker $tracker, int $limit, bool $force = false): int
    {
        try {
            return $tracker->syncPending($limit, $force);
        } catch (Throwable $exception) {
            Log::warning('Không cập nhật được trạng thái mail từ Brevo.', ['error' => $exception->getMessage()]);

            return 0;
        }
    }

    /**
     * @return array{today: int, delivered: int, awaiting: int, problem: int, success_rate: ?int}
     */
    private function stats(): array
    {
        $counts = EmailLog::query()
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $count = fn (string ...$statuses) => (int) collect($statuses)->sum(fn (string $s) => (int) ($counts[$s] ?? 0));

        $delivered = $count(EmailLog::STATUS_DELIVERED);
        $problem = $count(EmailLog::STATUS_FAILED, EmailLog::STATUS_REJECTED);
        $finished = $delivered + $problem;

        return [
            'today' => EmailLog::query()->where('created_at', '>=', now()->startOfDay())->count(),
            'delivered' => $delivered,
            'awaiting' => $count(EmailLog::STATUS_SENT, EmailLog::STATUS_DEFERRED),
            'problem' => $problem,
            'success_rate' => $finished > 0 ? (int) round($delivered * 100 / $finished) : null,
        ];
    }
}
