<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Gọi Google Gemini API (generateContent) bằng HTTP client có sẵn của Laravel.
 */
class GeminiClient
{
    public const BASE_URL = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct(
        private readonly ?string $apiKey,
        private readonly string $model,
    ) {}

    public function isConfigured(): bool
    {
        return filled($this->apiKey);
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $messages  role là "user" hoặc "assistant"
     *
     * @throws GeminiException
     */
    public function generate(string $systemInstruction, array $messages): string
    {
        try {
            $response = Http::withHeaders(['x-goog-api-key' => (string) $this->apiKey])
                ->acceptJson()
                ->timeout(30)
                ->post(self::BASE_URL."/models/{$this->model}:generateContent", [
                    'systemInstruction' => ['parts' => [['text' => $systemInstruction]]],
                    'contents' => array_map(fn (array $message) => [
                        'role' => $message['role'] === 'assistant' ? 'model' : 'user',
                        'parts' => [['text' => $message['content']]],
                    ], $messages),
                    'generationConfig' => ['maxOutputTokens' => 1024],
                ]);
        } catch (ConnectionException $exception) {
            throw new GeminiException('Không kết nối được Gemini API: '.$exception->getMessage());
        }

        if ($response->failed()) {
            throw new GeminiException(
                "Gemini API lỗi HTTP {$response->status()}: ".Str::limit($response->body(), 300),
                $response->status(),
            );
        }

        $text = collect($response->json('candidates.0.content.parts', []))
            ->reject(fn (array $part) => $part['thought'] ?? false)
            ->pluck('text')
            ->filter()
            ->implode('');

        if (trim($text) === '') {
            $reason = $response->json('candidates.0.finishReason')
                ?? $response->json('promptFeedback.blockReason')
                ?? 'không rõ';

            throw new GeminiException("Gemini không trả về nội dung (lý do: {$reason}).");
        }

        return trim($text);
    }
}
