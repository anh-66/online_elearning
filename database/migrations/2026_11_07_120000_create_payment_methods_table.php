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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // 'card', 'momo', 'bank'
            $table->string('title'); // e.g. "Visa kết thúc bằng •••• 4242", "Ví MoMo (0912***678)"
            $table->string('subtitle')->nullable(); // e.g. "Hết hạn 04/28", "Chủ ví: NGUYEN VAN A"
            $table->string('icon')->default('logos:visa');
            $table->boolean('is_default')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
