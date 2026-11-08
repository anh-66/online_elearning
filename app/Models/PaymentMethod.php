<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'subtitle',
        'icon',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTitleAttribute(?string $value): string
    {
        if (! $value) {
            return '';
        }

        $cleaned = (string) preg_replace('/^(Visa|Mastercard)\s+/ui', '', $value);

        return mb_strtoupper(mb_substr($cleaned, 0, 1)).mb_substr($cleaned, 1);
    }
}
