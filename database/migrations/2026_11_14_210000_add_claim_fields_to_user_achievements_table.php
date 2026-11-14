<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_achievements', function (Blueprint $table) {
            $table->boolean('is_claimed')->default(false)->after('is_unlocked');
            $table->timestamp('claimed_at')->nullable()->after('is_claimed');
            $table->boolean('is_notified')->default(false)->after('claimed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_achievements', function (Blueprint $table) {
            $table->dropColumn(['is_claimed', 'claimed_at', 'is_notified']);
        });
    }
};
