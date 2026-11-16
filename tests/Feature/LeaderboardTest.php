<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use Database\Seeders\AchievementSeeder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AchievementSeeder::class);
    }

    public function test_leaderboard_page_loads_successfully(): void
    {
        $response = $this->get(route('leaderboard.index'));

        $response->assertOk();
        $response->assertSee('Bảng Xếp Hạng Điểm Tích Lũy');
        $response->assertSee('Danh sách Top 10');
    }

    public function test_bang_xep_hang_alias_redirects_to_leaderboard(): void
    {
        $response = $this->get('/bang-xep-hang');

        $response->assertRedirect(route('leaderboard.index'));
    }

    public function test_leaderboard_only_includes_students(): void
    {
        $student = User::factory()->create([
            'name' => 'Học Viên Xuất Sắc',
            'role' => User::ROLE_STUDENT,
        ]);
        $instructor = User::factory()->instructor()->create([
            'name' => 'Giảng Viên Mẫu',
        ]);
        $admin = User::factory()->admin()->create([
            'name' => 'Quản Trị Viên',
        ]);

        $achievement = Achievement::first();

        // Thêm thành tựu cho cả 3
        foreach ([$student, $instructor, $admin] as $u) {
            UserAchievement::create([
                'user_id' => $u->id,
                'achievement_id' => $achievement->id,
                'progress' => $achievement->criteria_goal,
                'is_unlocked' => true,
                'is_claimed' => true,
                'unlocked_at' => now(),
            ]);
        }

        $response = $this->get(route('leaderboard.index'));

        $response->assertOk();
        $response->assertSee('Học Viên Xuất Sắc');
        $response->assertDontSee('Giảng Viên Mẫu');
        $response->assertDontSee('Quản Trị Viên');
    }

    public function test_leaderboard_ranks_by_achievement_exp(): void
    {
        $topStudent = User::factory()->create([
            'name' => 'Nguyễn Top 1',
            'role' => User::ROLE_STUDENT,
        ]);
        $secondStudent = User::factory()->create([
            'name' => 'Trần Top 2',
            'role' => User::ROLE_STUDENT,
        ]);

        $achievements = Achievement::take(2)->get();

        // Top 1 nhận cả 2 thành tựu
        foreach ($achievements as $ach) {
            UserAchievement::create([
                'user_id' => $topStudent->id,
                'achievement_id' => $ach->id,
                'progress' => $ach->criteria_goal,
                'is_unlocked' => true,
                'is_claimed' => true,
                'unlocked_at' => now(),
            ]);
        }

        // Top 2 chỉ nhận 1 thành tựu
        UserAchievement::create([
            'user_id' => $secondStudent->id,
            'achievement_id' => $achievements[0]->id,
            'progress' => $achievements[0]->criteria_goal,
            'is_unlocked' => true,
            'is_claimed' => true,
            'unlocked_at' => now(),
        ]);

        $response = $this->get(route('leaderboard.index'));

        $response->assertOk();
        $response->assertSee('Nguyễn Top 1');
        $response->assertSee('Trần Top 2');

        /** @var Collection $topStudents */
        $topStudents = $response->viewData('topStudents');
        $this->assertEquals($topStudent->id, $topStudents->first()->id);
    }
}
