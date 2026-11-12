<?php

namespace App\Services;

use RuntimeException;

class GeminiException extends RuntimeException
{
    public function __construct(string $message, private readonly ?int $status = null)
    {
        parent::__construct($message);
    }

    /**
     * Gemini trả 429 khi vượt giới hạn gói miễn phí (theo phút hoặc theo ngày).
     */
    public function isRateLimited(): bool
    {
        return $this->status === 429;
    }
}
