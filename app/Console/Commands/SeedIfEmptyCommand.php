<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SeedIfEmptyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-if-empty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chạy seeder dữ liệu mẫu chỉ khi database chưa có người dùng nào (lần deploy đầu tiên).';

    /**
     * Seeder dùng updateOrCreate nên nếu chạy lại sẽ ghi đè ảnh banner, avatar, ảnh khóa học,
     * mật khẩu mẫu... mà admin đã sửa. Vì vậy chỉ seed khi database còn trống.
     */
    public function handle(): int
    {
        if (User::query()->exists()) {
            $this->info('Database đã có dữ liệu, bỏ qua seeder để không ghi đè ảnh và nội dung đã sửa.');

            return self::SUCCESS;
        }

        $this->info('Database còn trống, chạy seeder dữ liệu mẫu.');

        return $this->call('db:seed', ['--force' => true]);
    }
}
