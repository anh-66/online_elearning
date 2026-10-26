<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

#[Fillable([
    'chapter_id',
    'title',
    'content',
    'video_url',
    'attachment_path',
    'order',
])]
class Lesson extends Model
{
    use HasFactory;

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(LessonComment::class);
    }

    public function course(): HasOneThrough
    {
        return $this->hasOneThrough(
            Course::class,
            Chapter::class,
            'id',          // Foreign key on Chapter table (referenced by Course)
            'id',          // Foreign key on Course table
            'chapter_id',  // Local key on Lesson table
            'course_id'    // Local key on Chapter table
        );
    }
}
