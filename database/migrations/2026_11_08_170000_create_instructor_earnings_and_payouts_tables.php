<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('instructor_earnings')) {
            Schema::create('instructor_earnings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
                $table->foreignId('enrollment_id')->nullable()->constrained('enrollments')->onDelete('set null');
                $table->decimal('order_amount', 12, 2);
                $table->decimal('instructor_rate', 5, 2)->default(70.00);
                $table->decimal('admin_rate', 5, 2)->default(30.00);
                $table->decimal('instructor_share', 12, 2);
                $table->decimal('admin_share', 12, 2);
                $table->string('status')->default('available');
                $table->timestamp('available_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('payout_requests')) {
            Schema::create('payout_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
                $table->decimal('amount', 12, 2);
                $table->string('bank_name');
                $table->string('bank_account_number');
                $table->string('bank_account_name');
                $table->text('note')->nullable();
                $table->string('status')->default('pending');
                $table->text('admin_note')->nullable();
                $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_requests');
        Schema::dropIfExists('instructor_earnings');
    }
};
