<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Console\Command;

class ClearTestEnrollmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'enrollment:reset {email=student3@gmail.com} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset toàn bộ dữ liệu học tập (ghi danh, tiến độ học bài, lịch sử làm quiz, phương thức thanh toán) của học viên để kiểm thử.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("Không tìm thấy người dùng với email: {$email}");

            return Command::FAILURE;
        }

        $deletedEnrollments = Enrollment::where('user_id', $user->id)->delete();
        $deletedProgress = LessonProgress::where('user_id', $user->id)->delete();
        $deletedAttempts = QuizAttempt::where('user_id', $user->id)->delete();
        $deletedPaymentMethods = $user->paymentMethods()->delete();

        $this->info("Đã reset thành công dữ liệu của học viên: {$user->name} ({$email}):");
        $this->line("- Đã xóa {$deletedEnrollments} khóa học đã ghi danh.");
        $this->line("- Đã xóa {$deletedProgress} tiến độ hoàn thành bài học.");
        $this->line("- Đã xóa {$deletedAttempts} lượt làm bài kiểm tra trắc nghiệm.");
        $this->line("- Đã xóa {$deletedPaymentMethods} phương thức thanh toán đã lưu.");
        $this->info('Tài khoản hiện đã hoàn toàn sạch sẽ như học viên mới đăng ký.');

        return Command::SUCCESS;
    }
}
