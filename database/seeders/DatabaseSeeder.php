<?php

namespace Database\Seeders;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Demo catalog: category => list of [title, summary, level, price].
     *
     * @var array<string, list<array{0: string, 1: string, 2: CourseLevel, 3: int}>>
     */
    private const array CATALOG = [
        'Lập trình Web' => [
            ['Lập trình PHP & MySQL từ con số 0', 'Nắm vững PHP thuần, kết nối MySQL và xây dựng website động hoàn chỉnh.', CourseLevel::Beginner, 0],
            ['Laravel 13 cho người mới bắt đầu', 'Xây dựng ứng dụng web theo mô hình MVC với Laravel, Eloquent và Blade.', CourseLevel::Intermediate, 499000],
            ['HTML, CSS & Tailwind thực chiến', 'Thiết kế giao diện responsive hiện đại bằng HTML5, CSS3 và Tailwind CSS.', CourseLevel::Beginner, 199000],
        ],
        'Cơ sở dữ liệu' => [
            ['Thiết kế cơ sở dữ liệu quan hệ', 'Phân tích yêu cầu, vẽ sơ đồ ERD và chuẩn hoá dữ liệu đến 3NF.', CourseLevel::Intermediate, 299000],
            ['SQL nâng cao cho lập trình viên', 'Truy vấn JOIN, subquery, index và tối ưu hiệu năng trên MySQL.', CourseLevel::Advanced, 399000],
        ],
        'Khoa học dữ liệu' => [
            ['Python cho phân tích dữ liệu', 'Làm quen với Python, Pandas và trực quan hoá dữ liệu bằng Matplotlib.', CourseLevel::Beginner, 0],
            ['Nhập môn Machine Learning', 'Hiểu các thuật toán học máy cơ bản và áp dụng với scikit-learn.', CourseLevel::Advanced, 599000],
        ],
        'Kỹ năng mềm' => [
            ['Kỹ năng thuyết trình hiệu quả', 'Tự tin trình bày ý tưởng trước đám đông với cấu trúc rõ ràng.', CourseLevel::Beginner, 0],
            ['Quản lý thời gian cho sinh viên', 'Lập kế hoạch học tập, tránh trì hoãn và cân bằng cuộc sống.', CourseLevel::Beginner, 99000],
        ],
        'Ngoại ngữ' => [
            ['Tiếng Anh giao tiếp cơ bản', 'Luyện phản xạ nghe nói qua các tình huống giao tiếp hằng ngày.', CourseLevel::Beginner, 249000],
            ['Luyện thi TOEIC 650+', 'Chiến lược làm bài và luyện đề TOEIC Listening & Reading.', CourseLevel::Intermediate, 449000],
        ],
        'Thiết kế' => [
            ['Thiết kế UI/UX với Figma', 'Từ wireframe đến prototype: quy trình thiết kế sản phẩm số.', CourseLevel::Intermediate, 349000],
        ],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'name' => 'Admin System',
            'password' => '12345678',
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
        ]);

        $teachers = collect([
            ['Nguyễn Văn Minh', 'teacher@gmail.com'],
            ['Trần Thị Lan', 'lan.tran@gmail.com'],
            ['Lê Hoàng Phúc', 'phuc.le@gmail.com'],
        ])->map(fn (array $teacher) => User::updateOrCreate(['email' => $teacher[1]], [
            'name' => $teacher[0],
            'password' => '12345678',
            'role' => UserRole::Teacher,
            'bio' => 'Giảng viên với hơn 5 năm kinh nghiệm giảng dạy và làm việc thực tế.',
            'email_verified_at' => now(),
        ]));

        $students = collect([User::updateOrCreate(['email' => 'student@gmail.com'], [
            'name' => 'Phạm Minh Anh',
            'password' => '12345678',
            'role' => UserRole::Student,
            'email_verified_at' => now(),
        ])])->merge(User::factory(24)->create(['role' => UserRole::Student]));

        foreach (self::CATALOG as $categoryName => $courses) {
            $category = Category::firstOrCreate(['slug' => Str::slug($categoryName)], [
                'name' => $categoryName,
                'description' => "Các khoá học thuộc lĩnh vực {$categoryName}.",
            ]);

            foreach ($courses as [$title, $summary, $level, $price]) {
                $course = Course::firstOrCreate(['slug' => Str::slug($title)], [
                    'category_id' => $category->id,
                    'instructor_id' => $teachers->random()->id,
                    'title' => $title,
                    'summary' => $summary,
                    'description' => $this->courseDescription($title, $summary),
                    'price' => $price,
                    'level' => $level,
                    'status' => CourseStatus::Published,
                    'published_at' => now()->subDays(random_int(1, 150)),
                ]);

                if ($course->wasRecentlyCreated) {
                    $this->seedLessonsAndLearners($course, $students);
                }
            }
        }

        $this->seedAnnouncements($admin);
    }

    private function courseDescription(string $title, string $summary): string
    {
        return <<<HTML
            <h2>Giới thiệu khoá học</h2>
            <p>{$summary} Khoá học <strong>{$title}</strong> được thiết kế theo lộ trình từ cơ bản đến thực hành, phù hợp với sinh viên và người đi làm.</p>
            <h3>Bạn sẽ học được gì?</h3>
            <ul>
                <li>Nắm vững kiến thức nền tảng và thuật ngữ quan trọng.</li>
                <li>Thực hành qua các bài tập và dự án nhỏ sát thực tế.</li>
                <li>Tự tin áp dụng vào học tập và công việc.</li>
            </ul>
            <h3>Yêu cầu</h3>
            <p>Máy tính kết nối Internet và tinh thần ham học hỏi.</p>
            HTML;
    }

    /**
     * @param  Collection<int, User>  $students
     */
    private function seedLessonsAndLearners(Course $course, $students): void
    {
        $lessonTitles = ['Giới thiệu tổng quan', 'Cài đặt môi trường', 'Kiến thức nền tảng', 'Thực hành cơ bản', 'Bài tập tình huống', 'Dự án cuối khoá'];

        $lessons = collect($lessonTitles)->map(fn (string $lessonTitle, int $index) => $course->lessons()->create([
            'title' => 'Bài '.($index + 1).': '.$lessonTitle,
            'content' => "<p>Nội dung bài học <strong>{$lessonTitle}</strong> của khoá {$course->title}.</p><p>Hãy xem video, đọc tài liệu và hoàn thành bài tập trước khi chuyển sang bài tiếp theo.</p><blockquote>Mẹo: ghi chú lại những ý chính để ôn tập dễ dàng hơn.</blockquote>",
            'video_url' => $index === 0 ? 'https://www.youtube.com/watch?v=ysEN5RaKOlA' : null,
            'duration_minutes' => random_int(8, 40),
            'position' => $index + 1,
            'is_preview' => $index === 0,
        ]));

        foreach ($students->random(random_int(4, 12)) as $student) {
            $student->enrollments()->forceCreate([
                'course_id' => $course->id,
                'created_at' => now()->subDays(random_int(0, 170)),
            ]);
            $student->completedLessons()->syncWithoutDetaching($lessons->take(random_int(0, $lessons->count()))->pluck('id'));

            if (random_int(0, 1)) {
                $student->reviews()->create([
                    'course_id' => $course->id,
                    'rating' => random_int(3, 5),
                    'comment' => collect([
                        'Khoá học rất bổ ích, giảng viên giải thích dễ hiểu.',
                        'Nội dung thực tế, mình áp dụng được ngay vào đồ án.',
                        'Bài giảng chi tiết, mong có thêm bài tập nâng cao.',
                        'Rất đáng tiền, cảm ơn thầy cô!',
                    ])->random(),
                ]);
            }
        }
    }

    private function seedAnnouncements(User $admin): void
    {
        $announcements = [
            'Thông báo lịch học kỳ I năm học 2026 - 2027',
            'Ra mắt lộ trình học Lập trình Web Full-stack',
            'Hướng dẫn ghi danh và theo dõi tiến độ học tập',
            'Bảo trì hệ thống định kỳ cuối tháng',
            'Cuộc thi "Sáng tạo cùng E-Learning" chính thức khởi động',
        ];

        foreach ($announcements as $index => $title) {
            Announcement::firstOrCreate(['slug' => Str::slug($title)], [
                'user_id' => $admin->id,
                'title' => $title,
                'content' => "<p>{$title}. Sinh viên vui lòng theo dõi thường xuyên mục <strong>Thông báo</strong> để cập nhật thông tin mới nhất.</p><ul><li>Thời gian áp dụng: ngay khi thông báo được đăng.</li><li>Mọi thắc mắc xin liên hệ phòng Đào tạo.</li></ul>",
                'is_published' => true,
                'published_at' => now()->subDays($index * 3),
            ]);
        }
    }
}
