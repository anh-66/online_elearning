<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug',
    'name',
    'description',
    'icon',
    'category',
    'tier',
    'points',
    'criteria_type',
    'criteria_goal',
    'is_active',
    'sort_order',
])]
class Achievement extends Model
{
    use HasFactory;

    public const TIER_BRONZE = 'bronze';

    public const TIER_SILVER = 'silver';

    public const TIER_GOLD = 'gold';

    public const TIER_PLATINUM = 'platinum';

    public const TIER_DIAMOND = 'diamond';

    public const CATEGORY_LEARNING = 'learning';

    public const CATEGORY_STREAK = 'streak';

    public const CATEGORY_QUIZ = 'quiz';

    public const CATEGORY_COMMUNITY = 'community';

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'criteria_goal' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function userAchievements(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot(['progress', 'is_unlocked', 'unlocked_at'])
            ->withTimestamps();
    }

    /**
     * Tên hiển thị của cấp độ (Tier).
     */
    public function getTierLabelAttribute(): string
    {
        return match ($this->tier) {
            self::TIER_BRONZE => 'Đồng',
            self::TIER_SILVER => 'Bạc',
            self::TIER_GOLD => 'Vàng',
            self::TIER_PLATINUM => 'Bạch kim',
            self::TIER_DIAMOND => 'Kim cương',
            default => 'Thường',
        };
    }

    /**
     * Tên danh mục hiển thị tiếng Việt.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            self::CATEGORY_LEARNING => 'Học tập',
            self::CATEGORY_STREAK => 'Chuyên cần',
            self::CATEGORY_QUIZ => 'Thử thách Quiz',
            self::CATEGORY_COMMUNITY => 'Cộng đồng',
            default => 'Khác',
        };
    }
}
