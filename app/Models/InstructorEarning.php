<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorEarning extends Model
{
    use HasFactory;

    public const STATUS_AVAILABLE = 'available';

    public const STATUS_PAID = 'paid';

    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'instructor_id',
        'course_id',
        'enrollment_id',
        'order_amount',
        'instructor_rate',
        'admin_rate',
        'instructor_share',
        'admin_share',
        'status',
        'available_at',
    ];

    protected function casts(): array
    {
        return [
            'order_amount' => 'decimal:2',
            'instructor_rate' => 'decimal:2',
            'admin_rate' => 'decimal:2',
            'instructor_share' => 'decimal:2',
            'admin_share' => 'decimal:2',
            'available_at' => 'datetime',
        ];
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }
}
