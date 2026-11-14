<?php

namespace App\Console\Commands;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use App\Services\AchievementService;
use Illuminate\Console\Command;

class SimulateAchievementsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'achievement:test 
                            {email=student3@gmail.com : Email của học viên cần kiểm thử} 
                            {--all : Mở khóa toàn bộ 100% tất cả các thành tựu} 
                            {--partial : Thiết lập trạng thái hỗn hợp (đã mở, đang thực hiện, chưa mở)} 
                            {--reset : Reset toàn bộ thành tựu của tài khoản về 0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mô phỏng và kiểm thử nhanh các trạng thái thành tựu (Đã mở khóa, Đang thực hiện, Chưa mở) cho học viên.';

    /**
     * Execute the console command.
     */
    public function handle(AchievementService $service): int
    {
        $email = (string) $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("Không tìm thấy tài khoản với email: {$email}");

            return Command::FAILURE;
        }

        $achievements = Achievement::where('is_active', true)->get();

        if ($achievements->isEmpty()) {
            $this->error('Chưa có danh sách thành tựu nào trong database. Vui lòng chạy php artisan db:seed --class=AchievementSeeder trước.');

            return Command::FAILURE;
        }

        if ($this->option('reset')) {
            UserAchievement::where('user_id', $user->id)->delete();
            $this->info("Đã reset toàn bộ thành tựu của [{$user->name}] ({$email}) về trạng thái ban đầu.");

            return Command::SUCCESS;
        }

        if ($this->option('all')) {
            foreach ($achievements as $ach) {
                UserAchievement::updateOrCreate([
                    'user_id' => $user->id,
                    'achievement_id' => $ach->id,
                ], [
                    'progress' => $ach->criteria_goal,
                    'is_unlocked' => true,
                    'unlocked_at' => now()->subDays(random_int(1, 15)),
                    'is_claimed' => true,
                    'claimed_at' => now()->subDays(random_int(1, 15)),
                    'is_notified' => true,
                ]);
            }

            $this->info("Đã MỞ KHÓA TOÀN BỘ ({$achievements->count()} thành tựu) cho học viên: {$user->name} ({$email})!");
            $this->line('Bây giờ bạn có thể vào http://localhost:8000/profile#achievements để kiểm tra.');

            return Command::SUCCESS;
        }

        // Mặc định hoặc --partial: Tạo trạng thái hỗn hợp
        // 1. Đã nhận thưởng hoàn tất (is_unlocked = true, is_claimed = true)
        $claimedSlugs = [
            'first_step',
            'knowledge_seeker',
            'streak_starter',
            'quiz_novice',
            'first_voice',
        ];

        // 2. Vừa hoàn thành - Chờ nhấn "Nhận ngay" (is_unlocked = true, is_claimed = false)
        $readyToClaimSlugs = [
            'first_enrollment',
            'streak_diligent',
            'perfect_score',
        ];

        // 3. Đang thực hiện
        $inProgressMap = [
            'dedicated_learner' => 14,   // 14 / 20
            'knowledge_master' => 28,    // 28 / 50
            'multi_scholar' => 2,        // 2 / 3
            'omni_learner' => 4,         // 4 / 6
            'streak_unbreakable' => 5,   // 5 / 7
            'streak_determined' => 8,    // 8 / 10
            'quiz_enthusiast' => 3,     // 3 / 5
            'quiz_conqueror' => 7,      // 7 / 10
            'active_discussant' => 3,   // 3 / 5
            'star_reviewer' => 2,       // 2 / 3
        ];

        foreach ($achievements as $ach) {
            $record = UserAchievement::firstOrNew([
                'user_id' => $user->id,
                'achievement_id' => $ach->id,
            ]);

            if (in_array($ach->slug, $claimedSlugs, true)) {
                $record->progress = $ach->criteria_goal;
                $record->is_unlocked = true;
                $record->unlocked_at = now()->subDays(random_int(3, 10));
                $record->is_claimed = true;
                $record->claimed_at = now()->subDays(random_int(1, 3));
                $record->is_notified = true;
            } elseif (in_array($ach->slug, $readyToClaimSlugs, true)) {
                $record->progress = $ach->criteria_goal;
                $record->is_unlocked = true;
                $record->unlocked_at = now();
                $record->is_claimed = false;
                $record->claimed_at = null;
                $record->is_notified = false; // Sẽ hiện thông báo toast góc dưới!
            } elseif (isset($inProgressMap[$ach->slug])) {
                $record->progress = $inProgressMap[$ach->slug];
                $record->is_unlocked = false;
                $record->unlocked_at = null;
                $record->is_claimed = false;
                $record->claimed_at = null;
                $record->is_notified = false;
            } else {
                $record->progress = 0;
                $record->is_unlocked = false;
                $record->unlocked_at = null;
                $record->is_claimed = false;
                $record->claimed_at = null;
                $record->is_notified = false;
            }

            $record->save();
        }

        $this->info("Đã thiết lập dữ liệu mô phỏng thành công cho học viên: {$user->name} ({$email}):");
        $this->line('- 5 huy hiệu: ĐÃ HOÀN THÀNH & ĐÃ NHẬN (Claimed)');
        $this->line("- 3 huy hiệu: MỚI HOÀN THÀNH - CHỜ NHẬN THƯỞNG (Ready to Claim - có nút 'Nhận ngay' và hiệu ứng cúp Trophy!)");
        $this->line('- 10 huy hiệu: ĐANG THỰC HIỆN (In Progress, có thanh %)');
        $this->line('- Còn lại: CHƯA MỞ KHÓA (Locked, ổ khóa xám)');
        $this->line("👉 Truy cập ngay: http://localhost:8000/profile#achievements để thử nút 'Nhận ngay'!");

        return Command::SUCCESS;
    }
}
