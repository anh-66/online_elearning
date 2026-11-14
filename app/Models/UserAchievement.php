<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'achievement_id',
    'progress',
    'is_unlocked',
    'unlocked_at',
    'is_claimed',
    'claimed_at',
    'is_notified',
])]
class UserAchievement extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'progress' => 'integer',
            'is_unlocked' => 'boolean',
            'unlocked_at' => 'datetime',
            'is_claimed' => 'boolean',
            'claimed_at' => 'datetime',
            'is_notified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }
}
