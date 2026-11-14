<?php

namespace App\Console\Commands;

use App\Services\BrevoDeliveryTracker;
use Illuminate\Console\Command;

class SyncEmailDeliveryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email-logs:sync {--limit=100 : Số mail tối đa hỏi Brevo trong một lần}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hỏi Brevo trạng thái thật (đã tới hộp thư, bị từ chối...) của các mail đang chờ trong trang Email đã gửi.';

    public function handle(BrevoDeliveryTracker $tracker): int
    {
        if (! $tracker->enabled()) {
            $this->warn('Chưa có BREVO_API_KEY trong .env nên không hỏi được Brevo.');

            return self::SUCCESS;
        }

        $changed = $tracker->syncPending(max(1, (int) $this->option('limit')), force: true);

        if ($problem = $tracker->lastProblem()) {
            $this->warn($problem);
        }

        $this->info("Đã cập nhật trạng thái của {$changed} mail.");

        return self::SUCCESS;
    }
}
