<?php

namespace App\Enums;

enum CourseStatus: string
{
    case Draft = 'draft';
    case Published = 'published';

    /**
     * Get the Vietnamese label of the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Bản nháp',
            self::Published => 'Đã xuất bản',
        };
    }

    /**
     * Get the badge color classes of the status.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Draft => 'bg-gray-100 text-gray-600',
            self::Published => 'bg-emerald-100 text-emerald-700',
        };
    }
}
