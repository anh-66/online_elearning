<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'student',
        ]);
    }

    public function test_public_registration_always_creates_a_student(): void
    {
        $response = $this->post('/register', [
            'name' => 'Instructor User',
            'email' => 'instructor@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'instructor',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'instructor@example.com',
            'role' => 'student',
        ]);
    }

    public function test_public_registration_always_creates_a_student_for_admin_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'role' => 'student',
        ]);
    }

    public function test_only_admins_can_create_instructor_accounts(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.instructors.store'), [
            'name' => 'Instructor User',
            'email' => 'created-instructor@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => 'created-instructor@example.com',
            'role' => User::ROLE_INSTRUCTOR,
        ]);
    }

    public function test_students_cannot_create_instructor_accounts(): void
    {
        $student = User::factory()->create();

        $response = $this->actingAs($student)->get(route('admin.instructors.create'));

        $response->assertForbidden();
        $this->assertDatabaseMissing('users', [
            'email' => 'created-instructor@example.com',
        ]);
    }
}
