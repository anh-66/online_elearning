<?php

namespace Tests\Feature\Auth;

use App\Mail\EmailOtpMail;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Contracts\User as GoogleUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    public function test_registration_sends_an_otp_and_redirects_to_the_code_page(): void
    {
        $this->post('/register', [
            'name' => 'Học Viên Mới',
            'email' => 'moi@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $user = User::where('email', 'moi@example.com')->firstOrFail();

        $this->assertNull($user->email_verified_at);
        Mail::assertSent(EmailOtpMail::class, fn (EmailOtpMail $mail) => $mail->hasTo('moi@example.com')
            && $mail->purpose === EmailOtp::PURPOSE_VERIFY_EMAIL
            && preg_match('/^\d{6}$/', $mail->code) === 1);
        Mail::assertSentCount(1);

        // Trang Welcome bị chặn cho tới khi nhập mã; trang nhập mã không gửi thêm mail.
        $this->get('/welcome')->assertRedirect(route('verification.notice'));
        $this->get(route('verification.notice'))
            ->assertOk()
            ->assertSee('moi@example.com')
            ->assertSee('Mã xác thực');
        Mail::assertSentCount(1);
    }

    public function test_correct_code_verifies_the_email_and_continues_to_the_intended_page(): void
    {
        $user = User::factory()->unverified()->create(['onboarding_completed' => false]);
        $code = $this->sendCodeTo($user);
        Event::fake([Verified::class]);

        $this->actingAs($user)->get('/welcome')->assertRedirect(route('verification.notice'));

        $this->post(route('verification.verify'), ['code' => $code])
            ->assertRedirect('/welcome');

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->assertDatabaseMissing('email_otps', ['user_id' => $user->id]);
    }

    public function test_verified_student_without_intended_page_goes_to_dashboard(): void
    {
        $user = User::factory()->unverified()->create(['onboarding_completed' => true]);
        $code = $this->sendCodeTo($user);

        $this->actingAs($user)
            ->post(route('verification.verify'), ['code' => $code])
            ->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_wrong_code_is_rejected_and_counts_attempts(): void
    {
        $user = User::factory()->unverified()->create();
        $code = $this->sendCodeTo($user);

        $this->actingAs($user)
            ->from(route('verification.notice'))
            ->post(route('verification.verify'), ['code' => $this->wrongCode($code)])
            ->assertRedirect(route('verification.notice'))
            ->assertSessionHasErrors(['code' => 'Mã không đúng. Bạn còn 4 lần thử.']);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $this->assertSame(1, EmailOtp::where('user_id', $user->id)->value('attempts'));
    }

    public function test_code_is_locked_after_five_wrong_attempts(): void
    {
        $user = User::factory()->unverified()->create();
        $code = $this->sendCodeTo($user);

        for ($i = 0; $i < 5; $i++) {
            $response = $this->actingAs($user)->post(route('verification.verify'), ['code' => $this->wrongCode($code)]);
        }

        $response->assertSessionHasErrors(['code' => 'Bạn đã nhập sai quá nhiều lần. Bấm "Gửi lại mã" để nhận mã mới.']);

        // Mã đúng cũng không dùng được nữa.
        $this->actingAs($user)->post(route('verification.verify'), ['code' => $code])
            ->assertSessionHasErrors('code');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_expired_code_is_rejected(): void
    {
        $user = User::factory()->unverified()->create();
        $code = $this->sendCodeTo($user);

        $this->travel(11)->minutes();

        $this->actingAs($user)->post(route('verification.verify'), ['code' => $code])
            ->assertSessionHasErrors(['code' => 'Mã đã hết hạn. Bấm "Gửi lại mã" để nhận mã mới.']);
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_code_must_be_six_digits(): void
    {
        $user = User::factory()->unverified()->create();
        $this->sendCodeTo($user);

        $this->actingAs($user)->post(route('verification.verify'), ['code' => '12ab'])
            ->assertSessionHasErrors(['code' => 'Mã gồm đúng 6 chữ số.']);
    }

    public function test_resend_waits_sixty_seconds_and_replaces_the_old_code(): void
    {
        $user = User::factory()->unverified()->create();
        $oldCode = $this->sendCodeTo($user);

        $this->actingAs($user)->post(route('verification.send'))
            ->assertSessionHas('otp_problem');
        Mail::assertSentCount(1);

        $this->travel(61)->seconds();

        $this->actingAs($user)->post(route('verification.send'))
            ->assertSessionHas('otp_notice');
        Mail::assertSentCount(2);

        $newCode = $this->lastSentCode();
        if ($newCode !== $oldCode) {
            $this->actingAs($user)->post(route('verification.verify'), ['code' => $oldCode])
                ->assertSessionHasErrors('code');
        }
        $this->actingAs($user)->post(route('verification.verify'), ['code' => $newCode]);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_at_most_five_codes_per_hour(): void
    {
        $user = User::factory()->unverified()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($user)->post(route('verification.send'))->assertSessionHas('otp_notice');
            $this->travel(61)->seconds();
        }

        $this->actingAs($user)->post(route('verification.send'))
            ->assertSessionHas('otp_problem');
        Mail::assertSentCount(5);
    }

    public function test_code_page_sends_a_new_code_when_the_old_one_expired(): void
    {
        $user = User::factory()->unverified()->create();
        $this->sendCodeTo($user);

        $this->travel(11)->minutes();

        $this->actingAs($user)->get(route('verification.notice'))
            ->assertOk()
            ->assertSee('Mã mới gồm 6 số đã được gửi tới email của bạn.');
        Mail::assertSentCount(2);
    }

    public function test_unverified_users_are_sent_to_the_code_page_everywhere(): void
    {
        $student = User::factory()->unverified()->create();
        $instructor = User::factory()->unverified()->create(['role' => User::ROLE_INSTRUCTOR]);

        $this->actingAs($student)->get('/my-courses')->assertRedirect(route('verification.notice'));
        $this->actingAs($student)->get('/')->assertRedirect(route('verification.notice'));
        $this->actingAs($student)->postJson('/attendance/check-in')->assertStatus(409);
        $this->actingAs($instructor)->get('/instructor/dashboard')->assertRedirect(route('verification.notice'));

        // Đăng xuất vẫn được.
        $this->actingAs($student)->post('/logout')->assertRedirect('/');
    }

    public function test_verified_users_and_guests_are_not_affected(): void
    {
        $this->get('/')->assertOk();

        $user = User::factory()->create();
        $this->actingAs($user)->get('/my-courses')->assertOk();
        $this->actingAs($user)->get(route('verification.notice'))->assertRedirect();
        Mail::assertNothingSent();
    }

    public function test_otp_can_be_switched_off(): void
    {
        config(['otp.enabled' => false]);

        $this->post('/register', [
            'name' => 'Không OTP',
            'email' => 'khong-otp@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ])->assertRedirect('/welcome');

        Mail::assertNothingSent();
        $this->get('/welcome')->assertOk();
    }

    public function test_existing_users_are_marked_verified_by_the_migration(): void
    {
        $user = User::factory()->unverified()->create();

        $migration = require database_path('migrations/2026_11_13_100100_mark_existing_users_email_verified.php');
        $migration->up();

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_instructors_created_by_admin_do_not_need_a_code(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)->post(route('admin.instructors.store'), [
            'name' => 'Giảng Viên',
            'email' => 'gv@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $this->assertNotNull(User::where('email', 'gv@example.com')->value('email_verified_at'));
        Mail::assertNothingSent();
    }

    public function test_new_google_users_are_verified_without_a_code(): void
    {
        $this->mockGoogleUser('google-otp-1', 'google-otp@example.com', 'Google OTP');

        $this->get(route('auth.google.callback'))->assertRedirect(route('onboarding.welcome'));

        $this->assertNotNull(User::where('email', 'google-otp@example.com')->value('email_verified_at'));
        Mail::assertNothingSent();
    }

    public function test_google_login_verifies_an_unverified_account_and_replaces_its_password(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'chua-xac-thuc@example.com',
            'password' => Hash::make('Password123'),
        ]);
        $this->mockGoogleUser('google-otp-2', 'chua-xac-thuc@example.com', 'Chủ Email');

        $this->get(route('auth.google.callback'));

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertFalse(Hash::check('Password123', $user->password));
    }

    private function sendCodeTo(User $user): string
    {
        $user->sendEmailVerificationNotification();

        return $this->lastSentCode();
    }

    private function lastSentCode(): string
    {
        return Mail::sent(EmailOtpMail::class)->last()->code;
    }

    private function wrongCode(string $code): string
    {
        return $code === '000000' ? '111111' : '000000';
    }

    private function mockGoogleUser(string $id, string $email, string $name): void
    {
        $googleUser = Mockery::mock(GoogleUser::class);
        $googleUser->shouldReceive('getId')->andReturn($id);
        $googleUser->shouldReceive('getEmail')->andReturn($email);
        $googleUser->shouldReceive('getName')->andReturn($name);
        $googleUser->shouldReceive('getAvatar')->andReturn(null);

        $driver = Mockery::mock();
        $driver->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($driver);
    }
}
