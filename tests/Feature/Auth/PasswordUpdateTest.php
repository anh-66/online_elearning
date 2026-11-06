<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password123',
                'password_confirmation' => 'new-password123',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertTrue(Hash::check('new-password123', $user->refresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password123',
                'password_confirmation' => 'new-password123',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect('/profile');
    }

    public function test_password_update_requires_letters_and_numbers(): void
    {
        $user = User::factory()->create();

        $responseWithoutNumbers = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'onlyletters',
                'password_confirmation' => 'onlyletters',
            ]);

        $responseWithoutNumbers
            ->assertSessionHasErrorsIn('updatePassword', 'password');

        $responseWithoutLetters = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => '12345678',
                'password_confirmation' => '12345678',
            ]);

        $responseWithoutLetters
            ->assertSessionHasErrorsIn('updatePassword', 'password');
    }
}
