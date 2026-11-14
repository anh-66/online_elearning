<?php

namespace Tests\Feature\Admin;

use App\Mail\EmailOtpMail;
use App\Models\EmailLog;
use App\Models\EmailOtp;
use App\Models\User;
use App\Services\EmailOtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_successful_send_is_recorded_without_the_code(): void
    {
        Mail::fake();
        $user = User::factory()->unverified()->create(['email' => 'hocvien@example.com']);

        app(EmailOtpService::class)->send($user, EmailOtp::PURPOSE_VERIFY_EMAIL);

        $log = EmailLog::sole();
        $this->assertSame(EmailLog::STATUS_SENT, $log->status);
        $this->assertSame('hocvien@example.com', $log->to_email);
        $this->assertSame($user->id, $log->user_id);
        $this->assertSame(EmailOtp::PURPOSE_VERIFY_EMAIL, $log->purpose);
        $this->assertNull($log->error);

        $code = Mail::sent(EmailOtpMail::class)->first()->code;
        $this->assertStringNotContainsString($code, json_encode($log->getAttributes()));
    }

    public function test_a_failed_brevo_send_is_recorded_with_the_error(): void
    {
        config([
            'mail.default' => 'brevo',
            'mail.mailers.brevo.key' => 'xkeysib-test-key',
            'mail.from.address' => 'edulearn.sender@gmail.com',
        ]);
        Http::fake(['api.brevo.com/*' => Http::response(['message' => 'Key not found'], 401)]);
        $user = User::factory()->create();

        app(EmailOtpService::class)->send($user, EmailOtp::PURPOSE_RESET_PASSWORD);

        $log = EmailLog::sole();
        $this->assertSame(EmailLog::STATUS_FAILED, $log->status);
        $this->assertSame('brevo', $log->mailer);
        $this->assertSame('Brevo: API trả về lỗi 401 (Key not found).', $log->error);
        $this->assertStringContainsString('Authorized IPs', $log->errorHint());
    }

    public function test_a_successful_brevo_send_keeps_the_message_id(): void
    {
        config([
            'mail.default' => 'brevo',
            'mail.mailers.brevo.key' => 'xkeysib-test-key',
            'mail.from.address' => 'edulearn.sender@gmail.com',
        ]);
        Http::fake(['api.brevo.com/*' => Http::response(['messageId' => '<abc@smtp-relay.mailin.fr>'], 201)]);
        $user = User::factory()->unverified()->create();

        app(EmailOtpService::class)->send($user, EmailOtp::PURPOSE_VERIFY_EMAIL);

        $this->assertSame('<abc@smtp-relay.mailin.fr>', EmailLog::sole()->message_id);
    }

    public function test_admin_sees_sent_and_failed_mails(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $student = User::factory()->create(['name' => 'Nguyễn Văn Học', 'email' => 'hoc@example.com']);
        $this->makeLog($student, EmailLog::STATUS_SENT, EmailOtp::PURPOSE_VERIFY_EMAIL);
        $this->makeLog($student, EmailLog::STATUS_FAILED, EmailOtp::PURPOSE_RESET_PASSWORD, 'Brevo: API trả về lỗi 401 (Key not found).');

        $this->actingAs($admin)->get(route('admin.email-logs.index'))
            ->assertOk()
            ->assertSee('Nguyễn Văn Học')
            ->assertSee('hoc@example.com')
            ->assertSee('Đã gửi đi')
            ->assertSee('Thất bại')
            ->assertSee('Brevo: API trả về lỗi 401 (Key not found).')
            ->assertSee('Authorized IPs')
            ->assertSee('Email đã gửi');
    }

    public function test_admin_can_filter_by_status_purpose_and_email(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $an = User::factory()->create(['email' => 'an@example.com']);
        $binh = User::factory()->create(['email' => 'binh@example.com']);
        $this->makeLog($an, EmailLog::STATUS_SENT, EmailOtp::PURPOSE_VERIFY_EMAIL);
        $this->makeLog($binh, EmailLog::STATUS_FAILED, EmailOtp::PURPOSE_RESET_PASSWORD, 'Brevo: lỗi thử nghiệm.');

        $this->actingAs($admin)->get(route('admin.email-logs.index', ['status' => 'failed']))
            ->assertOk()->assertSee('binh@example.com')->assertDontSee('an@example.com');

        $this->actingAs($admin)->get(route('admin.email-logs.index', ['purpose' => 'verify_email']))
            ->assertOk()->assertSee('an@example.com')->assertDontSee('binh@example.com');

        $this->actingAs($admin)->get(route('admin.email-logs.index', ['keyword' => 'binh']))
            ->assertOk()->assertSee('binh@example.com')->assertDontSee('an@example.com');
    }

    public function test_page_warns_when_mail_is_only_written_to_the_log(): void
    {
        config(['mail.default' => 'log']);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)->get(route('admin.email-logs.index'))
            ->assertOk()
            ->assertSee('MAIL_MAILER=log');
    }

    public function test_old_logs_are_cleaned_up_when_the_page_opens(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $old = $this->makeLog($admin, EmailLog::STATUS_SENT, EmailOtp::PURPOSE_VERIFY_EMAIL);
        $old->forceFill(['created_at' => now()->subDays(EmailLog::KEEP_DAYS + 1)])->save();
        $recent = $this->makeLog($admin, EmailLog::STATUS_SENT, EmailOtp::PURPOSE_VERIFY_EMAIL);

        $this->actingAs($admin)->get(route('admin.email-logs.index'))->assertOk();

        $this->assertModelMissing($old);
        $this->assertModelExists($recent);
    }

    public function test_students_and_instructors_cannot_open_the_page(): void
    {
        $student = User::factory()->create();
        $instructor = User::factory()->create(['role' => User::ROLE_INSTRUCTOR]);

        $this->actingAs($student)->get(route('admin.email-logs.index'))->assertForbidden();
        $this->actingAs($instructor)->get(route('admin.email-logs.index'))->assertForbidden();
    }

    private function makeLog(User $user, string $status, string $purpose, ?string $error = null): EmailLog
    {
        return EmailLog::create([
            'user_id' => $user->id,
            'to_email' => $user->email,
            'purpose' => $purpose,
            'mailer' => 'brevo',
            'status' => $status,
            'error' => $error,
        ]);
    }
}
