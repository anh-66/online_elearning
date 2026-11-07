<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bổ sung các cột thống kê kết quả, đạt/không đạt và chi tiết đáp án vào bảng quiz_attempts.
     */
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            if (! Schema::hasColumn('quiz_attempts', 'total_correct')) {
                $table->integer('total_correct')->default(0)->after('score');
            }

            if (! Schema::hasColumn('quiz_attempts', 'total_questions')) {
                $table->integer('total_questions')->default(0)->after('total_correct');
            }

            if (! Schema::hasColumn('quiz_attempts', 'is_passed')) {
                $table->boolean('is_passed')->default(false)->after('total_questions');
            }

            if (! Schema::hasColumn('quiz_attempts', 'selected_answers')) {
                $table->json('selected_answers')->nullable()->after('is_passed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['total_correct', 'total_questions', 'is_passed', 'selected_answers'] as $col) {
                if (Schema::hasColumn('quiz_attempts', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
