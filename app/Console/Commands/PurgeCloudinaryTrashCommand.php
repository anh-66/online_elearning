<?php

namespace App\Console\Commands;

use App\Services\CloudinaryTrashBin;
use Illuminate\Console\Command;

class PurgeCloudinaryTrashCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudinary:purge-trash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa vĩnh viễn trên Cloudinary những ảnh đã nằm trong thùng rác quá 7 ngày.';

    public function handle(CloudinaryTrashBin $trash): int
    {
        $result = $trash->purgeExpired();

        $this->info("Đã xóa vĩnh viễn {$result['purged']} ảnh hết hạn trong thùng rác.");

        if ($result['failed'] > 0) {
            $this->warn("{$result['failed']} ảnh chưa xóa được, sẽ thử lại lần sau.");
        }

        return self::SUCCESS;
    }
}
