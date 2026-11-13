<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\MessageConverter;
use Throwable;

/**
 * Gửi mail qua API giao dịch của Brevo bằng HTTPS (cổng 443).
 *
 * Render gói Free chặn các cổng SMTP 25/465/587, nên không gửi qua SMTP được.
 * Driver này gọi thẳng https://api.brevo.com/v3/smtp/email bằng Laravel HTTP Client,
 * không cần cài thêm package nào.
 */
class BrevoTransport extends AbstractTransport
{
    public function __construct(
        private readonly ?string $apiKey,
        private readonly string $endpoint = 'https://api.brevo.com/v3/smtp/email',
        private readonly int $timeout = 15,
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        if (! is_string($this->apiKey) || trim($this->apiKey) === '') {
            throw new TransportException('Brevo: chưa cấu hình BREVO_API_KEY.');
        }

        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $sender = $email->getFrom()[0] ?? $message->getEnvelope()->getSender();

        $payload = array_filter([
            'sender' => $this->address($sender),
            'to' => $this->addresses($email->getTo()),
            'cc' => $this->addresses($email->getCc()),
            'bcc' => $this->addresses($email->getBcc()),
            'replyTo' => isset($email->getReplyTo()[0]) ? $this->address($email->getReplyTo()[0]) : null,
            'subject' => (string) $email->getSubject(),
            'htmlContent' => $email->getHtmlBody() !== null ? (string) $email->getHtmlBody() : null,
            'textContent' => $email->getTextBody() !== null ? (string) $email->getTextBody() : null,
            'attachment' => $this->attachments($email->getAttachments()),
        ], fn ($value) => $value !== null && $value !== []);

        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'accept' => 'application/json',
            ])->timeout($this->timeout)->post($this->endpoint, $payload);
        } catch (Throwable $exception) {
            throw new TransportException('Brevo: không kết nối được API ('.$exception->getMessage().').', 0, $exception);
        }

        if ($response->failed()) {
            $reason = $response->json('message') ?? $response->body();

            throw new TransportException(sprintf('Brevo: API trả về lỗi %d (%s).', $response->status(), $reason));
        }

        if (is_string($messageId = $response->json('messageId'))) {
            $message->setMessageId($messageId);
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }

    /**
     * @return array{email: string, name?: string}
     */
    private function address(Address $address): array
    {
        return array_filter([
            'email' => $address->getAddress(),
            'name' => $address->getName(),
        ], fn (string $value) => $value !== '');
    }

    /**
     * @param  array<int, Address>  $addresses
     * @return array<int, array{email: string, name?: string}>
     */
    private function addresses(array $addresses): array
    {
        return array_map(fn (Address $address) => $this->address($address), $addresses);
    }

    /**
     * @param  array<int, \Symfony\Component\Mime\Part\DataPart>  $attachments
     * @return array<int, array{name: string, content: string}>
     */
    private function attachments(array $attachments): array
    {
        return array_map(fn ($attachment) => [
            'name' => $attachment->getFilename() ?? 'attachment',
            'content' => base64_encode($attachment->getBody()),
        ], $attachments);
    }
}
