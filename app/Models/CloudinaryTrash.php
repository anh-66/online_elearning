<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ảnh Cloudinary đang nằm trong thùng rác (vẫn còn trên Cloudinary cho tới khi bị xóa vĩnh viễn).
 */
#[Fillable(['path', 'name', 'folder', 'format', 'width', 'height', 'bytes', 'url', 'thumb_url', 'places', 'deleted_by', 'purge_at'])]
class CloudinaryTrash extends Model
{
    protected $table = 'cloudinary_trash';

    protected function casts(): array
    {
        return [
            'places' => 'array',
            'purge_at' => 'datetime',
            'width' => 'integer',
            'height' => 'integer',
            'bytes' => 'integer',
        ];
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Số ngày còn lại trước khi bị xóa vĩnh viễn (0 = sẽ xóa trong hôm nay / lần dọn kế tiếp).
     */
    public function daysLeft(): int
    {
        return max(0, (int) ceil(now()->diffInSeconds($this->purge_at, false) / 86400));
    }
}
