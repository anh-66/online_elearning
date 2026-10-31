<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $response->assertRedirect(route('dashboard', absolute: false));
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

    private function mockGoogleUser(string $id, string $email, string $name): void
    {
        $googleUser = Mockery::mock(GoogleUser::class);
        $googleUser->shouldReceive('getId')->andReturn($id);
        $googleUser->shouldReceive('getEmail')->andReturn($email);
        $googleUser->shouldReceive('getName')->andReturn($name);

        $driver = Mockery::mock();
        $driver->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($driver);
    }
}
