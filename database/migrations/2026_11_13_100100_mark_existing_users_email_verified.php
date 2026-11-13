<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Bật xác thực email bằng OTP chỉ áp dụng cho tài khoản đăng ký từ nay về sau.
     * Mọi tài khoản đang có được đánh dấu là đã xác thực để không ai bị khóa ngoài.
     */
    public function up(): void
    {
        DB::table('users')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
    }

    public function down(): void
    {
        // Không hoàn tác: không biết tài khoản nào trước đây chưa xác thực.
    }
};
