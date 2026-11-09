<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_demo_accounts_and_twenty_courses_repeatably(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 9);
        $this->assertSame(3, User::query()->where('role', User::ROLE_INSTRUCTOR)->count());
        $this->assertSame(5, User::query()->where('role', User::ROLE_STUDENT)->count());
        $this->assertDatabaseCount('courses', 20);
        $this->assertSame(0, Course::query()->whereDoesntHave('chapters')->count());

        $demoAccounts = User::query()
            ->whereIn('role', [User::ROLE_INSTRUCTOR, User::ROLE_STUDENT])
            ->get();

        foreach ($demoAccounts as $demoAccount) {
            $this->assertTrue(Hash::check('Password123', $demoAccount->password));
            $this->assertNotNull($demoAccount->email_verified_at);
        }
    }
}
