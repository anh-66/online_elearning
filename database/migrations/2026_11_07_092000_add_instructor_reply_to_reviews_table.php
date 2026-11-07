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
        Schema::table('reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('reviews', 'instructor_reply')) {
                $table->text('instructor_reply')->nullable()->after('comment');
            }
            if (! Schema::hasColumn('reviews', 'instructor_id')) {
                $table->foreignId('instructor_id')->nullable()->after('instructor_reply')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('reviews', 'replied_at')) {
                $table->timestamp('replied_at')->nullable()->after('instructor_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'instructor_id')) {
                $table->dropForeign(['instructor_id']);
                $table->dropColumn('instructor_id');
            }
            if (Schema::hasColumn('reviews', 'instructor_reply')) {
                $table->dropColumn('instructor_reply');
            }
            if (Schema::hasColumn('reviews', 'replied_at')) {
                $table->dropColumn('replied_at');
            }
        });
    }
};
