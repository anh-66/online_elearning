<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('notifications')
            ->whereIn('title', [
                'Chào mừng bạn đến với Nền tảng Học trực tuyến!',
                'Lịch bảo trì nâng cấp hệ thống máy chủ',
            ])
            ->delete();
    }

    public function down(): void {}
};
