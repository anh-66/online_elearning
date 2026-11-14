<?php

namespace Tests\Feature\Admin;

use App\Models\EmailLog;
use App\Models\EmailOtp;
use App\Models\User;
use App\Services\BrevoDeliveryTracker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EmailDeliveryTrackingTest extends TestCase
{
    use RefreshDatabase;

    private const EVENTS_URL = 'api.brevo.com/v3/smtp/statistics/events*';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'mail.default' => 'brevo',
            'mail.mailers.brevo.key' => 'xkeysib-test-key',
        ]);
    }

    public function test_events_are_turned_into_a_real_status(): void
    {
        $delivered = BrevoDeliveryTracker::outcome([
            ['event' => 'requests', 'date' => '2026-11-14T10:00:00.000+07:00'],
            ['event' => 'delivered', 'date' => '2026-11-14T10:00:03.000+07:00'],
        ]);
        $this->assertSame(EmailLog::STATUS_DELIVERED, $delivered['status']);
        $this->assertNull($delivered['opened_at']);

        $opened = BrevoDeliveryTracker::outcome([
            ['event' => 'opened', 'date' => '2026-11-14T10:05:00.000+07:00'],
            ['event' => 'delivered', 'date' => '2026-11-14T10:00:03.000+07:00'],
            ['event' => 'requests', 'date' => '2026-11-14T10:00:00.000+07:00'],
        ]);
        $this->assertSame(EmailLog::STATUS_DELIVERED, $opened['status']);
        $this->assertSame('2026-11-14 03:05:00', $opened['opened_at']->utc()->format('Y-m-d H:i:s'));

        $rejected = BrevoDeliveryTracker::outcome([
            ['event' => 'requests', 'date' => '2026-11-14T10:00:00.000+07:00'],
            ['event' => 'error', 'date' => '2026-11-14T10:00:01.000+07:00', 'reason' => 'sender hello@example.com is not valid'],
        ]);
        $this->assertSame(EmailLog::STATUS_REJECTED, $rejected['status']);
        $this->assertStringContainsString('sender hello@example.com is not valid', $rejected['detail']);

        $bounced = BrevoDeliveryTracker::outcome([['event' => 'hardBounces', 'date' => '2026-11-14T10:00:01.000+07:00', 'reason' => 'user unknown']]);
        $this->assertSame(EmailLog::STATUS_REJECTED, $bounced['status']);

        $deferred = BrevoDeliveryTracker::outcome([['event' => 'softBounces', 'date' => '2026-11-14T10:00:01.000+07:00']]);
        $this->assertSame(EmailLog::STATUS_DEFERRED, $deferred['status']);

        $waiting = BrevoDeliveryTracker::outcome([['event' => 'requests', 'date' => '2026-11-14T10:00:00.000+07:00']]);
        $this->assertNull($waiting['status']);
    }

    public function test_opening_the_page_asks_brevo_and_shows_the_mail_was_delivered(): void
    {
        Http::fake([self::EVENTS_URL => Http::response(['events' => [
            ['event' => 'requests', 'date' => now()->subSeconds(30)->toIso8601String()],
            ['event' => 'delivered', 'date' => now()->subSeconds(27)->toIso8601String()],
        ]])]);
        $log = $this->brevoLog();

        $this->actingAs($this->admin())->get(route('admin.email-logs.index'))
            ->assertOk()
            ->assertSee('Đã tới hộp thư');

        $this->assertSame(EmailLog::STATUS_DELIVERED, $log->fresh()->status);
        $this->assertNotNull($log->fresh()->delivered_at);
        Http::assertSent(fn (Request $request) => str_contains($request->url(), 'statistics/events')
            && $request['messageId'] === '<abc@smtp-relay.mailin.fr>'
            && $request->hasHeader('api-key', 'xkeysib-test-key'));
    }

    public function test_rejected_mail_shows_the_reason_and_how_to_fix_it(): void
    {
        Http::fake([self::EVENTS_URL => Http::response(['events' => [
            ['event' => 'error', 'date' => now()->toIso8601String(), 'reason' => 'sender hello@example.com is not valid'],
        ]])]);
        $log = $this->brevoLog();

        $this->actingAs($this->admin())->get(route('admin.email-logs.index'))
            ->assertOk()
            ->assertSee('Bị từ chối')
            ->assertSee('sender hello@example.com is not valid')
            ->assertSee('MAIL_FROM_ADDRESS');

        $this->assertSame(EmailLog::STATUS_REJECTED, $log->fresh()->status);
    }

    public function test_mail_that_only_went_to_the_log_is_not_checked(): void
    {
        Http::fake();
        $this->brevoLog(['mailer' => 'log', 'message_id' => null]);

        $this->actingAs($this->admin())->get(route('admin.email-logs.index'))
            ->assertOk()
            ->assertSee('Chỉ ghi log');

        Http::assertNothingSent();
    }

    public function test_a_mail_checked_moments_ago_is_not_asked_again(): void
    {
        Http::fake();
        $log = $this->brevoLog();
        $log->forceFill(['checked_at' => now()->subSeconds(5)])->save();

        $this->actingAs($this->admin())->get(route('admin.email-logs.index'))->assertOk();

        Http::assertNothingSent();
    }

    public function test_brevo_errors_are_shown_and_the_page_still_works(): void
    {
        Http::fake([self::EVENTS_URL => Http::response(['message' => 'Key not found'], 401)]);
        $log = $this->brevoLog();

        $this->actingAs($this->admin())->get(route('admin.email-logs.index'))
            ->assertOk()
            ->assertSee('Brevo trả về lỗi 401')
            ->assertSee('Chờ xác nhận');

        $this->assertSame(EmailLog::STATUS_SENT, $log->fresh()->status);

        // Brevo đang lỗi thì tạm không hỏi nữa để trang không bị chậm.
        $this->actingAs($this->admin())->get(route('admin.email-logs.index'))->assertOk();
        Http::assertSentCount(1);
    }

    public function test_refresh_button_updates_pending_mails(): void
    {
        Http::fake([self::EVENTS_URL => Http::response(['events' => [
            ['event' => 'delivered', 'date' => now()->toIso8601String()],
        ]])]);
        $log = $this->brevoLog();
        $log->forceFill(['checked_at' => now()])->save();

        $this->actingAs($this->admin())
            ->from(route('admin.email-logs.index'))
            ->post(route('admin.email-logs.sync'))
            ->assertRedirect(route('admin.email-logs.index'))
            ->assertSessionHas('success', 'Đã cập nhật trạng thái của 1 mail.');

        $this->assertSame(EmailLog::STATUS_DELIVERED, $log->fresh()->status);
    }

    public function test_sync_command_updates_pending_mails(): void
    {
        Http::fake([self::EVENTS_URL => Http::response(['events' => [
            ['event' => 'delivered', 'date' => now()->toIso8601String()],
        ]])]);
        $log = $this->brevoLog();

        $this->artisan('email-logs:sync')
            ->expectsOutput('Đã cập nhật trạng thái của 1 mail.')
            ->assertSuccessful();

        $this->assertSame(EmailLog::STATUS_DELIVERED, $log->fresh()->status);
    }

    public function test_stats_count_only_delivered_mail_as_success(): void
    {
        Http::fake();
        $this->brevoLog(['status' => EmailLog::STATUS_DELIVERED]);
        $this->brevoLog(['status' => EmailLog::STATUS_DELIVERED]);
        $this->brevoLog(['status' => EmailLog::STATUS_DELIVERED]);
        $this->brevoLog(['status' => EmailLog::STATUS_REJECTED, 'error' => 'Brevo không giao được mail (blocked).']);

        $this->actingAs($this->admin())->get(route('admin.email-logs.index'))
            ->assertOk()
            ->assertViewHas('stats', fn (array $stats) => $stats['delivered'] === 3
                && $stats['problem'] === 1
                && $stats['success_rate'] === 75);

        $this->actingAs($this->admin())->get(route('admin.email-logs.index', ['status' => 'problem']))
            ->assertOk()
            ->assertSee('Brevo không giao được mail (blocked).')
            ->assertDontSee('Tới hộp thư lúc');
    }

    public function test_code_page_learns_that_the_mail_arrived(): void
    {
        Http::fake([self::EVENTS_URL => Http::response(['events' => [
            ['event' => 'delivered', 'date' => now()->toIso8601String()],
        ]])]);
        $user = User::factory()->unverified()->create();
        $this->brevoLog(['user_id' => $user->id, 'to_email' => $user->email]);

        $this->actingAs($user)->getJson(route('verification.status'))
            ->assertOk()
            ->assertJson(['state' => 'delivered', 'final' => true]);
    }

    public function test_code_page_tells_the_user_when_the_problem_is_on_our_side(): void
    {
        Http::fake([self::EVENTS_URL => Http::response(['events' => [
            ['event' => 'error', 'date' => now()->toIso8601String(), 'reason' => 'sender hello@example.com is not valid'],
        ]])]);
        $user = User::factory()->unverified()->create();
        $this->brevoLog(['user_id' => $user->id, 'to_email' => $user->email]);

        $response = $this->actingAs($user)->getJson(route('verification.status'))
            ->assertOk()
            ->assertJson(['state' => 'rejected', 'final' => true]);

        $this->assertStringContainsString('lỗi cấu hình', $response->json('message'));
    }

    public function test_code_page_only_sees_the_users_own_mail(): void
    {
        Http::fake();
        $this->brevoLog();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->getJson(route('verification.status'))
            ->assertOk()
            ->assertJson(['state' => 'none']);

        Http::assertNothingSent();
    }

    public function test_verify_page_polls_the_status_only_when_sending_through_brevo(): void
    {
        $user = User::factory()->unverified()->create();
        EmailOtp::create([
            'user_id' => $user->id,
            'purpose' => EmailOtp::PURPOSE_VERIFY_EMAIL,
            'code_hash' => bcrypt('123456'),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
            'sent_at' => now(),
        ]);

        // Địa chỉ nằm trong x-data của Alpine nên được mã hoá kiểu JSON (http:\/\/...).
        $statusUrl = trim(json_encode(route('verification.status')), '"');

        $this->actingAs($user)->get(route('verification.notice'))
            ->assertOk()
            ->assertSee($statusUrl, false);

        config(['mail.default' => 'log']);

        $this->actingAs($user)->get(route('verification.notice'))
            ->assertOk()
            ->assertDontSee($statusUrl, false);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    private function brevoLog(array $attributes = []): EmailLog
    {
        $user = User::factory()->create();

        $log = EmailLog::create(array_merge([
            'user_id' => $user->id,
            'to_email' => $user->email,
            'purpose' => EmailOtp::PURPOSE_VERIFY_EMAIL,
            'mailer' => 'brevo',
            'status' => EmailLog::STATUS_SENT,
            'message_id' => '<abc@smtp-relay.mailin.fr>',
        ], array_intersect_key($attributes, array_flip(['user_id', 'to_email', 'purpose', 'mailer', 'status', 'message_id', 'error']))));

        return $log;
    }
}
