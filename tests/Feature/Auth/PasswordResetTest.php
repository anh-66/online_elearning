<?php

namespace Tests\Feature\Auth;

use App\Mail\EmailOtpMail;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $this->get('/forgot-password')
            ->assertOk()
            ->assertSee('Gửi mã xác nhận');
    }

    public function test_reset_code_is_emailed_and_only_the_code_step_is_shown(): void
    {
        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect(route('password.reset'));

        Mail::assertSent(EmailOtpMail::class, fn (EmailOtpMail $mail) => $mail->hasTo($user->email)
            && $mail->purpose === EmailOtp::PURPOSE_RESET_PASSWORD
            && str_contains($mail->envelope()->subject, $mail->code));

        $this->get(route('password.reset'))
            ->assertOk()
            ->assertSee($user->email)
            ->assertSee('Mã xác nhận')
            ->assertDontSee('name="password"', false);
    }

    public function test_code_page_without_requesting_a_code_goes_back_to_the_email_step(): void
    {
        $this->get(route('password.reset'))->assertRedirect(route('password.request'));
    }

    public function test_unknown_email_gets_the_same_answers_without_sending_mail(): void
    {
        $known = User::factory()->create();

        $unknownResponse = $this->post('/forgot-password', ['email' => 'khong-co@example.com']);
        $unknownResponse->assertRedirect(route('password.reset'));
        $this->post(route('password.verify-code'), ['code' => '123456'])
            ->assertSessionHasErrors(['code' => 'Mã không đúng. Bạn còn 4 lần thử.']);

        $knownResponse = $this->post('/forgot-password', ['email' => $known->email]);
        $knownResponse->assertRedirect(route('password.reset'));
        $code = $this->lastSentCode();
        $this->post(route('password.verify-code'), ['code' => $this->wrongCode($code)])
            ->assertSessionHasErrors(['code' => 'Mã không đúng. Bạn còn 4 lần thử.']);

        $this->assertSame(
            $knownResponse->getSession()->get('status'),
            $unknownResponse->getSession()->get('status'),
        );
        Mail::assertSentCount(1);
    }

    public function test_password_page_only_opens_after_the_correct_code(): void
    {
        $user = User::factory()->create();
        Event::fake([PasswordReset::class]);

        $this->post('/forgot-password', ['email' => $user->email]);
        $code = $this->lastSentCode();

        // Chưa nhập mã thì chưa vào được trang tạo mật khẩu mới.
        $this->get(route('password.new'))->assertRedirect(route('password.request'));

        $this->post(route('password.verify-code'), ['code' => $code])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('password.new'));

        $this->get(route('password.new'))
            ->assertOk()
            ->assertSee($user->email)
            ->assertSee('name="password"', false);

        $this->post(route('password.store'), [
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('NewPassword123', $user->fresh()->password));
        Event::assertDispatched(PasswordReset::class);

        // Xong rồi thì không quay lại đổi tiếp được.
        $this->get(route('password.new'))->assertRedirect(route('password.request'));

        $this->post('/login', ['email' => $user->email, 'password' => 'NewPassword123']);
        $this->assertAuthenticatedAs($user->fresh());
    }

    public function test_password_cannot_be_changed_without_entering_the_code(): void
    {
        $user = User::factory()->create(['password' => Hash::make('OldPassword123')]);

        $this->post('/forgot-password', ['email' => $user->email]);

        $this->post(route('password.store'), [
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ])
            ->assertRedirect(route('password.request'))
            ->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('OldPassword123', $user->fresh()->password));
    }

    public function test_wrong_code_keeps_the_user_on_the_code_step(): void
    {
        $user = User::factory()->create(['password' => Hash::make('OldPassword123')]);

        $this->post('/forgot-password', ['email' => $user->email]);
        $code = $this->lastSentCode();

        $this->from(route('password.reset'))
            ->post(route('password.verify-code'), ['code' => $this->wrongCode($code)])
            ->assertRedirect(route('password.reset'))
            ->assertSessionHasErrors(['code' => 'Mã không đúng. Bạn còn 4 lần thử.']);

        $this->get(route('password.new'))->assertRedirect(route('password.request'));
        $this->assertTrue(Hash::check('OldPassword123', $user->fresh()->password));
    }

    public function test_code_must_be_six_digits(): void
    {
        $user = User::factory()->create();
        $this->post('/forgot-password', ['email' => $user->email]);

        $this->post(route('password.verify-code'), ['code' => '12ab'])
            ->assertSessionHasErrors(['code' => 'Mã gồm đúng 6 chữ số.']);
    }

    public function test_expired_code_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);
        $code = $this->lastSentCode();
        $this->travel(11)->minutes();

        $this->post(route('password.verify-code'), ['code' => $code])
            ->assertSessionHasErrors(['code' => 'Mã đã hết hạn. Bấm "Gửi lại mã" để nhận mã mới.']);

        $this->get(route('password.new'))->assertRedirect(route('password.request'));
    }

    public function test_code_can_only_be_used_once(): void
    {
        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);
        $code = $this->lastSentCode();

        $this->post(route('password.verify-code'), ['code' => $code])->assertRedirect(route('password.new'));
        $this->post(route('password.verify-code'), ['code' => $code])->assertSessionHasErrors('code');
    }

    public function test_password_step_expires_after_ten_minutes(): void
    {
        $user = User::factory()->create(['password' => Hash::make('OldPassword123')]);

        $this->post('/forgot-password', ['email' => $user->email]);
        $this->post(route('password.verify-code'), ['code' => $this->lastSentCode()]);

        $this->travel(11)->minutes();

        $this->post(route('password.store'), [
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ])
            ->assertRedirect(route('password.request'))
            ->assertSessionHasErrors(['email' => 'Đã quá 10 phút kể từ lúc nhập mã. Bạn nhận mã mới để đặt lại mật khẩu nhé.']);

        $this->assertTrue(Hash::check('OldPassword123', $user->fresh()->password));
    }

    public function test_requesting_a_new_code_closes_the_password_step(): void
    {
        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);
        $this->post(route('password.verify-code'), ['code' => $this->lastSentCode()]);
        $this->get(route('password.new'))->assertOk();

        $this->travel(61)->seconds();
        $this->post('/forgot-password', ['email' => $user->email]);

        $this->get(route('password.new'))->assertRedirect(route('password.request'));
    }

    public function test_reset_code_cannot_verify_the_email(): void
    {
        $user = User::factory()->unverified()->create();

        $this->post('/forgot-password', ['email' => $user->email]);
        $resetCode = $this->lastSentCode();

        $this->actingAs($user)->post(route('verification.verify'), ['code' => $resetCode])
            ->assertSessionHasErrors('code');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_resetting_the_password_also_verifies_the_email(): void
    {
        $user = User::factory()->unverified()->create();

        $this->post('/forgot-password', ['email' => $user->email]);
        $this->post(route('password.verify-code'), ['code' => $this->lastSentCode()]);
        $this->post(route('password.store'), [
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ])->assertRedirect(route('login'));

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_blocked_users_do_not_receive_a_code(): void
    {
        $user = User::factory()->create(['status' => User::STATUS_BLOCKED]);

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertRedirect(route('password.reset'));

        Mail::assertNothingSent();
    }

    private function lastSentCode(): string
    {
        return Mail::sent(EmailOtpMail::class)->last()->code;
    }

    private function wrongCode(string $code): string
    {
        return $code === '000000' ? '111111' : '000000';
    }
}
