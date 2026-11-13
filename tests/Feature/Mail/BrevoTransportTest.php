<?php

namespace Tests\Feature\Mail;

use App\Mail\EmailOtpMail;
use App\Models\EmailOtp;
use App\Models\User;
use App\Services\EmailOtpResult;
use App\Services\EmailOtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class BrevoTransportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'mail.default' => 'brevo',
            'mail.mailers.brevo.key' => 'xkeysib-test-key',
            'mail.from.address' => 'edulearn.sender@gmail.com',
            'mail.from.name' => 'EduLearn',
        ]);
    }

    public function test_mail_is_sent_through_the_brevo_api(): void
    {
        Http::fake([
            'api.brevo.com/*' => Http::response(['messageId' => '<abc@smtp-relay.mailin.fr>'], 201),
        ]);
        $user = User::factory()->create(['name' => 'Học Viên', 'email' => 'hocvien@example.com']);

        Mail::to($user)->send(new EmailOtpMail($user, '123456', EmailOtp::PURPOSE_VERIFY_EMAIL, 10));

        Http::assertSent(function (Request $request) {
            $payload = $request->data();

            return $request->url() === 'https://api.brevo.com/v3/smtp/email'
                && $request->method() === 'POST'
                && $request->hasHeader('api-key', 'xkeysib-test-key')
                && $payload['sender'] === ['email' => 'edulearn.sender@gmail.com', 'name' => 'EduLearn']
                && $payload['to'] === [['email' => 'hocvien@example.com', 'name' => 'Học Viên']]
                && $payload['subject'] === 'Mã xác thực email EduLearn: 123456'
                && str_contains($payload['htmlContent'], '123456')
                && str_contains($payload['textContent'], '123456');
        });
    }

    public function test_api_errors_are_reported_as_transport_errors(): void
    {
        Http::fake([
            'api.brevo.com/*' => Http::response(['code' => 'unauthorized', 'message' => 'Key not found'], 401),
        ]);
        $user = User::factory()->create();

        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('Brevo: API trả về lỗi 401 (Key not found).');

        Mail::to($user)->send(new EmailOtpMail($user, '123456', EmailOtp::PURPOSE_VERIFY_EMAIL, 10));
    }

    public function test_missing_api_key_is_reported_clearly(): void
    {
        config(['mail.mailers.brevo.key' => null]);
        Http::fake();
        $user = User::factory()->create();

        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('Brevo: chưa cấu hình BREVO_API_KEY.');

        Mail::to($user)->send(new EmailOtpMail($user, '123456', EmailOtp::PURPOSE_VERIFY_EMAIL, 10));
    }

    public function test_failed_otp_mail_keeps_the_code_and_writes_it_to_the_log(): void
    {
        Http::fake([
            'api.brevo.com/*' => Http::response(['message' => 'Unauthorized IP'], 401),
        ]);
        Log::spy();
        $user = User::factory()->unverified()->create();

        $result = app(EmailOtpService::class)->send($user, EmailOtp::PURPOSE_VERIFY_EMAIL);

        $this->assertTrue($result->is(EmailOtpResult::SEND_FAILED));
        $this->assertDatabaseHas('email_otps', ['user_id' => $user->id, 'purpose' => EmailOtp::PURPOSE_VERIFY_EMAIL]);
        Log::shouldHaveReceived('error')->withArgs(fn (string $message, array $context) => str_contains($message, 'Brevo')
            && preg_match('/^\d{6}$/', $context['code']) === 1
            && str_contains($context['error'], 'Unauthorized IP'));
    }
}
