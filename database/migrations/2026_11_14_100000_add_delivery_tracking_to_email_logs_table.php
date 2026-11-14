<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Trạng thái thật của mail sau khi Brevo nhận: đã tới hộp thư, bị từ chối, đã mở...
     * Đọc từ API sự kiện của Brevo (xem App\Services\BrevoDeliveryTracker).
     */
    public function up(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->string('delivery_event', 32)->nullable()->after('error');
            $table->timestamp('delivered_at')->nullable()->after('delivery_event');
            $table->timestamp('opened_at')->nullable()->after('delivered_at');
            $table->timestamp('checked_at')->nullable()->after('opened_at');
        });
    }

    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropColumn(['delivery_event', 'delivered_at', 'opened_at', 'checked_at']);
        });
    }
};
