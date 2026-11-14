<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use App\Models\UserAchievement;
use App\Services\AchievementService;
use Database\Seeders\AchievementSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AchievementSeeder::class);
    }

    public function test_profile_page_displays_achievements_data(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
        $response->assertViewHas('achievementsData');
        $response->assertSee('Thành tựu');
        $response->assertSee('Hành trình Chinh phục Tri thức');
    }

    public function test_completing_first_lesson_unlocks_first_step_achievement(): void
    {
        $user = User::factory()->create();
        $instructor = User::factory()->instructor()->create();

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Khóa học Laravel Master',
            'slug' => 'khoa-hoc-laravel-master-'.fake()->unique()->randomNumber(5),
            'price' => 0,
            'level' => Course::LEVEL_BEGINNER,
            'status' => Course::STATUS_PUBLISHED,
        ]);
        $chapter = Chapter::create(['course_id' => $course->id, 'title' => 'Chương 1', 'order' => 1]);
        $lesson = Lesson::create(['chapter_id' => $chapter->id, 'title' => 'Bài 1', 'order' => 1]);

        LessonProgress::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        $service = app(AchievementService::class);
        $data = $service->getProfileAchievementsData($user);

        $this->assertGreaterThanOrEqual(1, $data['unlocked_count']);

        $firstStep = collect($data['achievements'])->firstWhere('slug', 'first_step');
        $this->assertNotNull($firstStep);
        $this->assertTrue($firstStep->is_unlocked);
        $this->assertSame(1, $firstStep->progress);
        $this->assertSame(100, $firstStep->percentage);
    }

    public function test_export_data_includes_user_achievements(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile/export-data');

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('"achievements":', $content);
    }

    public function test_categories_and_tier_labels_are_formatted_properly(): void
    {
        $achievement = Achievement::where('slug', 'first_step')->first();
        $this->assertNotNull($achievement);
        $this->assertSame('Đồng', $achievement->tier_label);
        $this->assertSame('Học tập', $achievement->category_label);
    }

    public function test_user_can_claim_completed_achievement(): void
    {
        $user = User::factory()->create();
        $achievement = Achievement::where('slug', 'first_step')->firstOrFail();

        UserAchievement::create([
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
            'progress' => $achievement->criteria_goal,
            'is_unlocked' => true,
            'is_claimed' => false,
            'unlocked_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson(route('profile.achievements.claim', $achievement));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'achievement' => [
                'id' => $achievement->id,
                'name' => $achievement->name,
                'points' => $achievement->points,
            ],
        ]);

        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
            'is_claimed' => true,
        ]);
    }
}
