<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chứng chỉ hoàn thành khóa học. Tên học viên, tên khóa học và giảng viên được lưu lại
     * tại thời điểm cấp để chứng chỉ không bị đổi khi khóa học sửa tên sau này.
     */
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('recipient_name');
            $table->string('course_title');
            $table->string('instructor_name')->nullable();
            $table->unsignedInteger('lessons_count')->default(0);
            $table->timestamp('issued_at');
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
