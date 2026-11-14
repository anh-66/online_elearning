<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Contracts\User as GoogleUser;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_contains_google_login_link(): void
    {
        $response = $this->get('/login');

        $response->assertOk()
            ->assertSee(route('auth.google'));
    }

    public function test_google_login_creates_and_authenticates_a_user(): void
    {
        $this->mockGoogleUser('google-123', 'google@example.com', 'Google User');

        $response = $this->get(route('auth.google.callback'));

        $user = User::where('email', 'google@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->password);
        $response->assertRedirect(route('onboarding.welcome', absolute: false));
    }

    public function test_google_login_links_an_existing_user_by_email(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'google_id' => null,
        ]);
        $this->mockGoogleUser('google-456', 'existing@example.com', 'Existing User');

        $response = $this->get(route('auth.google.callback'));

        $this->assertAuthenticatedAs($user->fresh());
        $this->assertSame('google-456', $user->fresh()->google_id);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_google_login_saves_the_google_avatar_for_a_new_user(): void
    {
        $avatar = 'https://lh3.googleusercontent.com/a/new-user=s96-c';
        $this->mockGoogleUser('google-789', 'avatar@example.com', 'Avatar User', $avatar);

        $this->get(route('auth.google.callback'));

        $user = User::where('email', 'avatar@example.com')->firstOrFail();

        $this->assertSame($avatar, $user->avatar);
        $this->assertSame($avatar, $user->avatar_url);
    }

    public function test_google_login_sets_avatar_for_an_existing_user_without_one(): void
    {
        $user = User::factory()->create([
            'email' => 'no-avatar@example.com',
            'avatar' => null,
        ]);
        $avatar = 'https://lh3.googleusercontent.com/a/existing=s96-c';
        $this->mockGoogleUser('google-321', 'no-avatar@example.com', 'No Avatar', $avatar);

        $this->get(route('auth.google.callback'));

        $this->assertSame($avatar, $user->fresh()->avatar);
    }

    public function test_google_login_keeps_an_uploaded_avatar(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/me.png', 'image');
        $user = User::factory()->create([
            'email' => 'uploaded@example.com',
            'avatar' => 'avatars/me.png',
        ]);
        $this->mockGoogleUser('google-654', 'uploaded@example.com', 'Uploaded', 'https://lh3.googleusercontent.com/a/other=s96-c');

        $this->get(route('auth.google.callback'));

        $this->assertSame('avatars/me.png', $user->fresh()->avatar);
    }

    public function test_blocked_users_cannot_login_with_google(): void
    {
        User::factory()->blocked()->create([
            'email' => 'blocked@example.com',
            'google_id' => 'google-blocked',
        ]);
        $this->mockGoogleUser('google-blocked', 'blocked@example.com', 'Blocked User');

        $response = $this->get(route('auth.google.callback'));

        $this->assertGuest();
        $response->assertRedirect(route('login'))
            ->assertSessionHas('error');
    }

    private function mockGoogleUser(string $id, string $email, string $name, ?string $avatar = null): void
    {
        $googleUser = Mockery::mock(GoogleUser::class);
        $googleUser->shouldReceive('getId')->andReturn($id);
        $googleUser->shouldReceive('getEmail')->andReturn($email);
        $googleUser->shouldReceive('getName')->andReturn($name);
        $googleUser->shouldReceive('getAvatar')->andReturn($avatar);

        $driver = Mockery::mock();
        $driver->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($driver);
    }
}
