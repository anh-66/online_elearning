<?php

use App\Models\Notification;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Notification::updateOrCreate([
            'title' => 'Chào mừng bạn đến với Nền tảng Học trực tuyến!',
        ], [
            'content' => 'Hệ thống đã chính thức đi vào hoạt động với nhiều khóa học công nghệ miễn phí và hấp dẫn.',
            'type' => 'system',
        ]);

        Notification::updateOrCreate([
            'title' => 'Lịch bảo trì nâng cấp hệ thống máy chủ',
        ], [
            'content' => 'Hệ thống sẽ tiến hành bảo trì định kỳ từ 02:00 đến 04:00 ngày Chủ Nhật tới. Rất mong quý học viên thông cảm.',
            'type' => 'system',
        ]);
    }

    public function down(): void
    {
        Notification::whereIn('title', [
            'Chào mừng bạn đến với Nền tảng Học trực tuyến!',
            'Lịch bảo trì nâng cấp hệ thống máy chủ',
        ])->delete();
    }
};
