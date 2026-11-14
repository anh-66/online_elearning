<?php

namespace App\Services;

use App\Models\EmailLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

/**
 * Hỏi Brevo xem mail đã thật sự tới hộp thư chưa.
 *
 * Brevo trả lời "đã nhận" ngay khi web gửi yêu cầu, vài giây sau mới giao (hoặc từ chối).
 * Lớp này đọc sự kiện của từng mail qua API GET /v3/smtp/statistics/events?messageId=...
 * Gói Free dùng được, không cần webhook nên chạy được cả ở máy local.
 */
class BrevoDeliveryTracker
{
    private const PROBLEM_KEY = 'brevo-delivery-tracker:problem';

    private const PAUSE_KEY = 'brevo-delivery-tracker:pause';

    public function enabled(): bool
    {
        return filled($this->apiKey());
    }

    /**
     * Lỗi gần nhất khi đọc trạng thái từ Brevo (để admin biết vì sao trạng thái không cập nhật).
     */
    public function lastProblem(): ?string
    {
        $problem = Cache::get(self::PROBLEM_KEY);

        return is_string($problem) ? $problem : null;
    }

    /**
     * Hỏi lại các mail còn đang chờ, mới nhất trước. $force bỏ qua thời gian chờ giữa hai lần hỏi.
     */
    public function syncPending(int $limit = 10, bool $force = false): int
    {
        if (! $this->enabled() || (! $force && Cache::has(self::PAUSE_KEY))) {
            return 0;
        }

        return $this->sync($this->pendingQuery($force)->latest('id')->limit($limit)->get());
    }

    /**
     * Mail cần hỏi lại: đang chờ / đang thử lại, hoặc đã tới nhưng chưa mở (trong 1 ngày đầu).
     */
    public function pendingQuery(bool $force = false): Builder
    {
        // Không hỏi lại quá dày: mail đang chờ 20 giây một lần, mail đã tới (chờ xem đã mở chưa) 10 phút một lần.
        $notCheckedFor = fn (int $seconds) => fn (Builder $query) => $query->when(! $force, fn (Builder $q) => $q
            ->where(fn (Builder $w) => $w->whereNull('checked_at')->orWhere('checked_at', '<=', now()->subSeconds($seconds))));

        return EmailLog::query()
            ->where('mailer', 'brevo')
            ->whereNotNull('message_id')
            ->where('created_at', '>=', now()->subDays(EmailLog::TRACK_DAYS))
            ->where(function (Builder $query) use ($notCheckedFor) {
                $query
                    ->where(function (Builder $q) use ($notCheckedFor) {
                        $q->whereIn('status', [EmailLog::STATUS_SENT, EmailLog::STATUS_DEFERRED]);
                        $notCheckedFor(20)($q);
                    })
                    ->orWhere(function (Builder $q) use ($notCheckedFor) {
                        $q->where('status', EmailLog::STATUS_DELIVERED)
                            ->whereNull('opened_at')
                            ->where('created_at', '>=', now()->subDay());
                        $notCheckedFor(600)($q);
                    });
            });
    }

    /**
     * Hỏi Brevo trạng thái của các mail này (gọi song song) và lưu lại. Trả về số mail đổi trạng thái.
     *
     * @param  iterable<EmailLog>  $logs
     */
    public function sync(iterable $logs): int
    {
        $logs = collect($logs)
            ->filter(fn (EmailLog $log) => $log->isTrackable())
            ->keyBy(fn (EmailLog $log) => (string) $log->getKey());

        if ($logs->isEmpty() || ! $this->enabled()) {
            return 0;
        }

        try {
            $responses = Http::pool(function (Pool $pool) use ($logs) {
                foreach ($logs as $key => $log) {
                    $pool->as($key)
                        ->withHeaders(['api-key' => $this->apiKey(), 'accept' => 'application/json'])
                        ->connectTimeout(3)
                        ->timeout(6)
                        ->get($this->endpoint(), [
                            'messageId' => $log->message_id,
                            'days' => EmailLog::TRACK_DAYS + 1,
                            'limit' => 100,
                        ]);
                }
            });
        } catch (Throwable $exception) {
            $this->pause('Không kết nối được Brevo để đọc trạng thái mail ('.Str::limit($exception->getMessage(), 120).').');

            return 0;
        }

        $changed = 0;
        $problem = null;

        foreach ($logs as $key => $log) {
            $response = $responses[$key] ?? null;

            if (! $response instanceof Response) {
                $problem = 'Không kết nối được Brevo để đọc trạng thái mail. Trạng thái sẽ được cập nhật lần sau.';

                continue;
            }

            if ($response->failed()) {
                $problem = sprintf(
                    'Brevo trả về lỗi %d khi đọc trạng thái mail (%s).%s',
                    $response->status(),
                    Str::limit((string) ($response->json('message') ?? 'không rõ lý do'), 160),
                    in_array($response->status(), [401, 403], true) ? ' Kiểm tra BREVO_API_KEY và Brevo > Security > Authorized IPs.' : '',
                );

                continue;
            }

            $changed += $this->apply($log, (array) $response->json('events', [])) ? 1 : 0;
        }

        $problem === null ? Cache::forget(self::PROBLEM_KEY) : $this->pause($problem);

        return $changed;
    }

    /**
     * Lưu kết quả đọc được vào nhật ký. Trả về true nếu trạng thái (hoặc lúc mở mail) thay đổi.
     *
     * @param  array<int, array<string, mixed>>  $events
     */
    public function apply(EmailLog $log, array $events): bool
    {
        $outcome = self::outcome($events);
        $before = [$log->status, $log->opened_at?->getTimestamp()];

        $changes = ['checked_at' => now()];

        if ($outcome['event'] !== null) {
            $changes['delivery_event'] = $outcome['event'];
        }

        if ($outcome['status'] !== null) {
            $changes['status'] = $outcome['status'];
            $changes['delivered_at'] = $outcome['delivered_at'];
            $changes['opened_at'] = $outcome['opened_at'];
            $changes['error'] = $outcome['detail'];
        }

        $log->forceFill($changes)->save();

        return $before !== [$log->status, $log->opened_at?->getTimestamp()];
    }

    /**
     * Rút ra trạng thái cuối cùng từ danh sách sự kiện Brevo trả về cho một mail.
     *
     * @param  array<int, array<string, mixed>>  $events
     * @return array{status: ?string, event: ?string, detail: ?string, delivered_at: ?Carbon, opened_at: ?Carbon}
     */
    public static function outcome(array $events): array
    {
        $events = array_values(array_filter($events, 'is_array'));
        usort($events, fn (array $a, array $b) => strcmp((string) ($a['date'] ?? ''), (string) ($b['date'] ?? '')));

        $deliveredAt = null;
        $openedAt = null;
        $rejected = null;
        $deferred = null;
        $spam = false;
        $lastEvent = null;

        foreach ($events as $event) {
            $name = (string) ($event['event'] ?? '');
            $at = self::date($event['date'] ?? null);
            $kind = self::kind($name);

            if ($kind === null) {
                continue;
            }

            $lastEvent = Str::limit($name, 32, '');
            $reason = trim((string) ($event['reason'] ?? ''));

            switch ($kind) {
                case 'opened':
                    $openedAt ??= $at;
                    $deliveredAt ??= $at;
                    break;
                case 'delivered':
                    $deliveredAt ??= $at;
                    break;
                case 'rejected':
                    $rejected = ['event' => $name, 'reason' => $reason];
                    break;
                case 'deferred':
                    $deferred = ['event' => $name, 'reason' => $reason];
                    break;
                case 'spam':
                    $spam = true;
                    break;
            }
        }

        $status = null;
        $detail = null;

        if ($deliveredAt !== null) {
            $status = EmailLog::STATUS_DELIVERED;
            $detail = $spam ? 'Người nhận đã đánh dấu mail là spam.' : null;
        } elseif ($rejected !== null) {
            $status = EmailLog::STATUS_REJECTED;
            $detail = self::detail('Brevo không giao được mail', $rejected);
        } elseif ($deferred !== null) {
            $status = EmailLog::STATUS_DEFERRED;
            $detail = self::detail('Hộp thư người nhận tạm từ chối, Brevo đang gửi lại', $deferred);
        }

        return [
            'status' => $status,
            'event' => $lastEvent,
            'detail' => $detail,
            'delivered_at' => $deliveredAt,
            'opened_at' => $openedAt,
        ];
    }

    /**
     * Nhóm tên sự kiện của Brevo: requests, delivered, opened, clicks, hardBounces, softBounces,
     * blocked, invalid, error, deferred, spam, unsubscribed, loadedByProxy...
     */
    private static function kind(string $event): ?string
    {
        $event = Str::lower(preg_replace('/[^a-z]/i', '', $event) ?? '');

        return match (true) {
            $event === '' => null,
            $event === 'delivered' => 'delivered',
            str_contains($event, 'open'), str_contains($event, 'click'), str_contains($event, 'proxy') => 'opened',
            str_contains($event, 'softbounce'), str_contains($event, 'deferred') => 'deferred',
            str_contains($event, 'bounce'), str_contains($event, 'blocked'), str_contains($event, 'invalid'), str_contains($event, 'error') => 'rejected',
            str_contains($event, 'spam'), str_contains($event, 'complaint') => 'spam',
            str_contains($event, 'request'), $event === 'sent' => 'requested',
            default => 'other',
        };
    }

    /**
     * @param  array{event: string, reason: string}  $event
     */
    private static function detail(string $prefix, array $event): string
    {
        return Str::limit($prefix.' ('.$event['event'].')'.($event['reason'] !== '' ? ': '.$event['reason'] : '.'), 1000);
    }

    private static function date(mixed $value): ?Carbon
    {
        if (! is_string($value) || $value === '') {
            return now();
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return now();
        }
    }

    /**
     * Ghi lại lỗi và tạm ngưng hỏi Brevo 2 phút để trang admin không bị chậm khi Brevo lỗi.
     */
    private function pause(string $problem): void
    {
        Cache::put(self::PROBLEM_KEY, $problem, now()->addMinutes(30));
        Cache::put(self::PAUSE_KEY, true, now()->addMinutes(2));
    }

    private function apiKey(): ?string
    {
        $key = config('mail.mailers.brevo.key');

        return is_string($key) && trim($key) !== '' ? trim($key) : null;
    }

    private function endpoint(): string
    {
        return (string) config('mail.mailers.brevo.events_endpoint', 'https://api.brevo.com/v3/smtp/statistics/events');
    }
}
