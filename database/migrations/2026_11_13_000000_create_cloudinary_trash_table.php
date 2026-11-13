<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thùng rác ảnh Cloudinary: ảnh admin đã xóa, giữ lại 7 ngày trước khi xóa vĩnh viễn trên Cloudinary.
     */
    public function up(): void
    {
        Schema::create('cloudinary_trash', function (Blueprint $table) {
            $table->id();
            $table->string('path')->unique();
            $table->string('name');
            $table->string('folder')->default('');
            $table->string('format', 20)->default('');
            $table->unsignedInteger('width')->default(0);
            $table->unsignedInteger('height')->default(0);
            $table->unsignedBigInteger('bytes')->default(0);
            $table->text('url');
            $table->text('thumb_url');
            $table->json('places')->nullable();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('purge_at')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloudinary_trash');
    }
};
