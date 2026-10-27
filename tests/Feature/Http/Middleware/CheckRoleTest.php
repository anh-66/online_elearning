<?php

namespace Tests\Feature\Http\Middleware;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CheckRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_request_to_admin_redirects_to_login(): void
    {
        $this->get('/admin/dashboard')
            ->assertRedirectToRoute('login');
    }

    public function test_unauthenticated_request_to_instructor_redirects_to_login(): void
    {
        $this->get('/instructor/dashboard')
            ->assertRedirectToRoute('login');
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk();
    }

    public function test_instructor_can_access_instructor_dashboard(): void
    {
        $instructor = User::factory()->instructor()->create();

        $this->actingAs($instructor)
            ->get('/instructor/dashboard')
            ->assertOk();
    }

    public function test_admin_can_access_instructor_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/instructor/dashboard')
            ->assertOk();
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function adminUnauthorizedRoles(): array
    {
        return [
            'student' => [User::ROLE_STUDENT],
            'instructor' => [User::ROLE_INSTRUCTOR],
        ];
    }

    #[DataProvider('adminUnauthorizedRoles')]
    public function test_non_admin_cannot_access_admin_dashboard(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_student_cannot_access_instructor_dashboard(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)
            ->get('/instructor/dashboard')
            ->assertForbidden();
    }

    public function test_blocked_admin_is_logged_out_when_visiting_admin(): void
    {
        $admin = User::factory()->admin()->blocked()->create();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $this->assertGuest();
        $response
            ->assertRedirectToRoute('login')
            ->assertSessionHas('error');
    }
}
