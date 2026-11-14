<?php

namespace Tests\Feature\Auth;

use App\Models\EmailLog;
use App\Models\User;
use App\Services\EmailOtpResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OtpResendLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    public function test_wait_before_resending_grows_after_each_code(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->post(route('verification.send'))->assertSessionHas('otp_notice');

        $this->travel(61)->seconds();
        $this->actingAs($user)->post(route('verification.send'))->assertSessionHas('otp_notice');

        // Sau mã thứ hai phải chờ 2 phút.
        $this->travel(61)->seconds();
        $this->actingAs($user)->post(route('verification.send'))
            ->assertSessionHas('otp_problem', 'Mã vừa được gửi. Bạn đợi 59 giây nữa rồi mới gửi lại được.');

        $this->travel(60)->seconds();
        $this->actingAs($user)->post(route('verification.send'))->assertSessionHas('otp_notice');

        Mail::assertSentCount(3);
    }

    public function test_code_page_shows_the_wait_and_the_sends_left(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)->get(route('verification.notice'))
            ->assertOk()
            ->assertSee('Còn 4 lần gửi trong giờ này')
            ->assertSee('until: Date.now() + 60 * 1000', false);
    }

    public function test_forgot_password_does_not_send_again_during_the_wait(): void
    {
        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect(route('password.reset'))
            ->assertSessionHas('status');

        $this->from(route('password.reset'))
            ->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect(route('password.reset'))
            ->assertSessionMissing('status')
            ->assertSessionHas('otp_problem', fn (string $message) => str_contains($message, 'Bạn đợi'));

        Mail::assertSentCount(1);

        $this->get(route('password.reset'))
            ->assertOk()
            ->assertSee('Còn 4 lần gửi trong giờ này');
    }

    public function test_unknown_email_is_throttled_the_same_way(): void
    {
        $known = User::factory()->create();

        $this->post('/forgot-password', ['email' => $known->email]);
        $knownAgain = $this->post('/forgot-password', ['email' => $known->email]);

        $this->post('/forgot-password', ['email' => 'khong-co@example.com']);
        $unknownAgain = $this->post('/forgot-password', ['email' => 'khong-co@example.com']);

        $this->assertSame(
            $knownAgain->getSession()->get('otp_problem'),
            $unknownAgain->getSession()->get('otp_problem'),
        );
        Mail::assertSentCount(1);
    }

    public function test_daily_cap_stops_sending_and_is_logged(): void
    {
        config(['otp.max_sends_per_day' => 1]);
        $first = User::factory()->unverified()->create();
        $second = User::factory()->unverified()->create();

        $this->actingAs($first)->post(route('verification.send'))->assertSessionHas('otp_notice');

        $this->actingAs($second)->post(route('verification.send'))
            ->assertSessionHas('otp_problem', EmailOtpResult::dailyLimit()->message());

        Mail::assertSentCount(1);
        $this->assertDatabaseHas('email_logs', [
            'user_id' => $second->id,
            'status' => EmailLog::STATUS_FAILED,
        ]);
    }

    public function test_daily_cap_on_forgot_password_answers_every_email_the_same(): void
    {
        config(['otp.max_sends_per_day' => 1]);
        $known = User::factory()->create();
        User::factory()->unverified()->create()->sendEmailVerificationNotification();

        $this->post('/forgot-password', ['email' => $known->email])
            ->assertSessionHasErrors(['email' => EmailOtpResult::dailyLimit()->message()]);
        $this->post('/forgot-password', ['email' => 'khong-co@example.com'])
            ->assertSessionHasErrors(['email' => EmailOtpResult::dailyLimit()->message()]);

        Mail::assertSentCount(1);
    }

    public function test_one_ip_cannot_request_codes_for_many_emails(): void
    {
        config(['otp.max_requests_per_ip_per_hour' => 3]);

        foreach (['a@example.com', 'b@example.com', 'c@example.com'] as $email) {
            $this->post('/forgot-password', ['email' => $email])->assertRedirect(route('password.reset'));
        }

        $this->from(route('password.request'))
            ->post('/forgot-password', ['email' => 'd@example.com'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHasErrors('email');

        $this->assertStringContainsString('quá nhiều mã', session('errors')->first('email'));
    }

    public function test_wait_is_written_in_words(): void
    {
        $this->assertSame('45 giây', EmailOtpResult::duration(45));
        $this->assertSame('1 phút', EmailOtpResult::duration(60));
        $this->assertSame('1 phút 30 giây', EmailOtpResult::duration(90));
        $this->assertSame('10 phút', EmailOtpResult::duration(600));
        $this->assertSame('42 phút', EmailOtpResult::duration(2501));
    }
}
