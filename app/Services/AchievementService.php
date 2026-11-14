<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Support\Collection;

class AchievementService
{
    /**
     * Đồng bộ và tính toán lại tiến trình toàn bộ thành tựu của học viên dựa trên dữ liệu thực tế.
     */
    public function syncUserAchievements(User $user): void
    {
        $metrics = $this->gatherUserMetrics($user);
        $achievements = Achievement::where('is_active', true)->get();

        foreach ($achievements as $achievement) {
            $currentValue = $metrics[$achievement->criteria_type] ?? 0;
            $goal = max(1, $achievement->criteria_goal);
            $progress = min($currentValue, $goal);
            $isUnlocked = $currentValue >= $goal;

            $record = UserAchievement::firstOrNew([
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
            ]);

            // Thành tựu khi đã mở khóa thì bảo lưu, không bao giờ bị hạ bậc hoặc thu hồi
            if ($record->is_unlocked) {
                continue;
            }

            $record->progress = max((int) ($record->progress ?? 0), $progress);
            $record->is_unlocked = false;

            if ($isUnlocked || $record->progress >= $goal) {
                $record->is_unlocked = true;
                $record->progress = $goal;
                $record->unlocked_at = $record->unlocked_at ?? now();
                $record->is_claimed = (bool) ($record->is_claimed ?? false);
                $record->is_notified = (bool) ($record->is_notified ?? false);
            }

            $record->save();
        }
    }

    /**
     * Lấy danh sách thành tựu mới hoàn thành mà chưa thông báo, và đánh dấu là đã thông báo.
     *
     * @return array<object>
     */
    public function popUnnotifiedAchievements(User $user): array
    {
        $records = UserAchievement::where('user_id', $user->id)
            ->where('is_unlocked', true)
            ->where('is_notified', false)
            ->with('achievement')
            ->get();

        if ($records->isEmpty()) {
            return [];
        }

        $results = [];
        foreach ($records as $record) {
            $record->is_notified = true;
            $record->save();

            if ($record->achievement) {
                $results[] = (object) [
                    'id' => $record->achievement->id,
                    'name' => $record->achievement->name,
                    'points' => $record->achievement->points,
                    'icon' => $record->achievement->icon,
                ];
            }
        }

        return $results;
    }

    /**
     * Thu thập các chỉ số học tập, chuyên cần, quiz, bình luận của học viên.
     *
     * @return array<string, int>
     */
    public function gatherUserMetrics(User $user): array
    {
        $lessonsCompleted = $user->lessonProgress()
            ->where('is_completed', true)
            ->count();

        $coursesEnrolled = $user->enrollments()->count();

        $coursesCompleted = $user->certificates()->count();

        $streakDays = (int) ($user->attendances()->max('streak') ?? 0);

        // Đếm số bài quiz đã thi đậu (is_passed = true hoặc điểm score >= 80)
        $quizzesPassed = $user->quizAttempts()
            ->where(function ($query) {
                $query->where('is_passed', true)
                    ->orWhere('score', '>=', 80);
            })
            ->distinct('quiz_id')
            ->count('quiz_id');

        // Đếm số bài quiz đạt điểm tuyệt đối 100%
        $perfectQuizzes = $user->quizAttempts()
            ->where('score', '>=', 100)
            ->distinct('quiz_id')
            ->count('quiz_id');

        $commentsWritten = $user->comments()->count();

        $reviewsWritten = $user->reviews()->count();

        return [
            'lessons_completed' => $lessonsCompleted,
            'courses_enrolled' => $coursesEnrolled,
            'courses_completed' => $coursesCompleted,
            'streak_days' => $streakDays,
            'quizzes_passed' => $quizzesPassed,
            'perfect_quizzes' => $perfectQuizzes,
            'comments_written' => $commentsWritten,
            'reviews_written' => $reviewsWritten,
        ];
    }

    /**
     * Lấy đầy đủ dữ liệu thống kê và danh sách thành tựu cho trang hồ sơ của học viên.
     *
     * @return array{
     *     total_achievements: int,
     *     unlocked_count: int,
     *     locked_count: int,
     *     completion_percentage: int,
     *     total_points_earned: int,
     *     total_points_available: int,
     *     highlighted: Collection,
     *     categories: array<string, string>,
     *     achievements: Collection
     * }
     */
    public function getProfileAchievementsData(User $user): array
    {
        // Tự động đồng bộ để người dùng luôn thấy dữ liệu mới nhất
        $this->syncUserAchievements($user);

        $achievements = Achievement::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $userRecords = UserAchievement::where('user_id', $user->id)
            ->get()
            ->keyBy('achievement_id');

        $mapped = $achievements->map(function (Achievement $ach) use ($userRecords) {
            $userRecord = $userRecords->get($ach->id);
            $progress = $userRecord?->progress ?? 0;
            $isUnlocked = (bool) ($userRecord?->is_unlocked ?? false);
            $isClaimed = (bool) ($userRecord?->is_claimed ?? false);
            $unlockedAt = $userRecord?->unlocked_at;
            $claimedAt = $userRecord?->claimed_at;
            $goal = max(1, $ach->criteria_goal);
            $percentage = min(100, (int) round(($progress / $goal) * 100));
            $canClaim = $isUnlocked && ! $isClaimed;

            return (object) [
                'id' => $ach->id,
                'slug' => $ach->slug,
                'name' => $ach->name,
                'description' => $ach->description,
                'icon' => $ach->icon,
                'category' => $ach->category,
                'category_label' => $ach->category_label,
                'tier' => $ach->tier,
                'tier_label' => $ach->tier_label,
                'points' => $ach->points,
                'criteria_type' => $ach->criteria_type,
                'criteria_goal' => $goal,
                'progress' => $progress,
                'percentage' => $percentage,
                'is_unlocked' => $isUnlocked,
                'is_claimed' => $isClaimed,
                'can_claim' => $canClaim,
                'unlocked_at' => $unlockedAt,
                'claimed_at' => $claimedAt,
            ];
        });

        $totalCount = $mapped->count();
        $unlockedCount = $mapped->where('is_unlocked', true)->count();
        $claimedCount = $mapped->where('is_claimed', true)->count();
        $canClaimCount = $mapped->where('can_claim', true)->count();
        $lockedCount = max(0, $totalCount - $unlockedCount);
        $completionPercentage = $totalCount > 0 ? (int) round(($unlockedCount / $totalCount) * 100) : 0;

        $pointsEarned = $mapped->where('is_claimed', true)->sum('points');
        $pointsAvailable = $mapped->sum('points');

        // Lấy tối đa 4 huy hiệu nổi bật (ưu tiên mới mở khóa hoặc cấp cao)
        $highlighted = $mapped->where('is_unlocked', true)
            ->sortByDesc('points')
            ->take(4)
            ->values();

        $pendingNotifications = $this->popUnnotifiedAchievements($user);

        return [
            'total_achievements' => $totalCount,
            'unlocked_count' => $unlockedCount,
            'claimed_count' => $claimedCount,
            'can_claim_count' => $canClaimCount,
            'locked_count' => $lockedCount,
            'completion_percentage' => $completionPercentage,
            'total_points_earned' => $pointsEarned,
            'total_points_available' => $pointsAvailable,
            'highlighted' => $highlighted,
            'pending_notifications' => $pendingNotifications,
            'categories' => [
                'all' => 'Tất cả',
                Achievement::CATEGORY_LEARNING => 'Học tập',
                Achievement::CATEGORY_STREAK => 'Chuyên cần',
                Achievement::CATEGORY_QUIZ => 'Thử thách Quiz',
                Achievement::CATEGORY_COMMUNITY => 'Cộng đồng',
            ],
            'achievements' => $mapped,
        ];
    }
}
