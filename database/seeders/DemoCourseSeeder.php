<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use RuntimeException;

class DemoCourseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Add the remaining courses to the two full courses in DatabaseSeeder.
     */
    public function run(): void
    {
        $instructorEmails = [
            'instructor1@gmail.com',
            'instructor2@gmail.com',
            'instructor@gmail.com',
        ];
        $instructors = User::query()
            ->whereIn('email', $instructorEmails)
            ->get()
            ->keyBy('email');

        if ($instructors->count() !== count($instructorEmails)) {
            throw new RuntimeException('Run DatabaseSeeder first to create the demo instructor accounts.');
        }

        $categoryDefinitions = [
            [
                'slug' => 'lap-trinh-web',
                'name' => 'Lập trình Web',
                'icon' => 'code',
                'description' => 'Phát triển giao diện, ứng dụng web và cơ sở dữ liệu.',
            ],
            [
                'slug' => 'khoa-hoc-du-lieu-ai',
                'name' => 'Khoa học Dữ liệu & AI',
                'icon' => 'chart-bar',
                'description' => 'Phân tích dữ liệu, học máy và trí tuệ nhân tạo.',
            ],
            [
                'slug' => 'thiet-ke-do-hoa-ui-ux',
                'name' => 'Thiết kế Đồ họa & UI/UX',
                'icon' => 'palette',
                'description' => 'Thiết kế đồ họa, giao diện và trải nghiệm người dùng.',
            ],
            [
                'slug' => 'marketing-kinh-doanh',
                'name' => 'Marketing & Kinh doanh',
                'icon' => 'briefcase',
                'description' => 'Marketing số, kinh doanh và kỹ năng phân tích.',
            ],
            [
                'slug' => 'ngoai-ngu',
                'name' => 'Ngoại ngữ',
                'icon' => 'language',
                'description' => 'Các khóa học ngoại ngữ phục vụ học tập và công việc.',
            ],
            [
                'slug' => 'ky-nang-ca-nhan',
                'name' => 'Kỹ năng cá nhân',
                'icon' => 'lightbulb',
                'description' => 'Phát triển kỹ năng học tập, làm việc và quản lý thời gian.',
            ],
        ];

        $categoryIds = [];

        foreach ($categoryDefinitions as $categoryDefinition) {
            $category = Category::updateOrCreate(
                ['slug' => $categoryDefinition['slug']],
                $categoryDefinition,
            );
            $categoryIds[$category->slug] = $category->id;
        }

        $courseDefinitions = [
            [
                'title' => 'HTML và CSS Responsive từ cơ bản đến thực hành',
                'slug' => 'html-css-responsive-tu-co-ban-den-thuc-hanh',
                'category' => 'lap-trinh-web',
                'description' => 'Nắm vững HTML, CSS và xây dựng giao diện thích ứng trên điện thoại, máy tính bảng và máy tính.',
                'price' => 0,
                'level' => Course::LEVEL_BEGINNER,
            ],
            [
                'title' => 'JavaScript hiện đại và lập trình bất đồng bộ',
                'slug' => 'javascript-hien-dai-va-lap-trinh-bat-dong-bo',
                'category' => 'lap-trinh-web',
                'description' => 'Thực hành JavaScript hiện đại, thao tác DOM, Promise và gọi API trong ứng dụng web.',
                'price' => 249000,
                'level' => Course::LEVEL_BEGINNER,
            ],
            [
                'title' => 'Xây dựng giao diện với React và Vite',
                'slug' => 'xay-dung-giao-dien-voi-react-va-vite',
                'category' => 'lap-trinh-web',
                'description' => 'Tạo ứng dụng React có cấu trúc rõ ràng, quản lý trạng thái và kết nối dữ liệu.',
                'price' => 599000,
                'level' => Course::LEVEL_INTERMEDIATE,
            ],
            [
                'title' => 'Thiết kế REST API với Laravel',
                'slug' => 'thiet-ke-rest-api-voi-laravel',
                'category' => 'lap-trinh-web',
                'description' => 'Xây dựng REST API bằng Laravel với xác thực, kiểm tra dữ liệu và xử lý lỗi.',
                'price' => 699000,
                'level' => Course::LEVEL_INTERMEDIATE,
            ],
            [
                'title' => 'MySQL thực hành cho lập trình viên',
                'slug' => 'mysql-thuc-hanh-cho-lap-trinh-vien',
                'category' => 'lap-trinh-web',
                'description' => 'Thiết kế bảng, viết truy vấn SQL và tối ưu các thao tác dữ liệu phổ biến.',
                'price' => 299000,
                'level' => Course::LEVEL_BEGINNER,
            ],
            [
                'title' => 'Python nền tảng qua bài tập thực tế',
                'slug' => 'python-nen-tang-qua-bai-tap-thuc-te',
                'category' => 'khoa-hoc-du-lieu-ai',
                'description' => 'Làm quen với cú pháp Python, hàm, cấu trúc dữ liệu và cách tổ chức chương trình.',
                'price' => 0,
                'level' => Course::LEVEL_BEGINNER,
            ],
            [
                'title' => 'Machine Learning ứng dụng với Scikit-learn',
                'slug' => 'machine-learning-ung-dung-voi-scikit-learn',
                'category' => 'khoa-hoc-du-lieu-ai',
                'description' => 'Chuẩn bị dữ liệu, huấn luyện và đánh giá các mô hình học máy phổ biến.',
                'price' => 799000,
                'level' => Course::LEVEL_INTERMEDIATE,
            ],
            [
                'title' => 'Deep Learning nhập môn với PyTorch',
                'slug' => 'deep-learning-nhap-mon-voi-pytorch',
                'category' => 'khoa-hoc-du-lieu-ai',
                'description' => 'Tìm hiểu mạng nơ-ron, vòng lặp huấn luyện và xây dựng mô hình với PyTorch.',
                'price' => 999000,
                'level' => Course::LEVEL_ADVANCED,
            ],
            [
                'title' => 'Phân tích dữ liệu với SQL và Power BI',
                'slug' => 'phan-tich-du-lieu-voi-sql-va-power-bi',
                'category' => 'khoa-hoc-du-lieu-ai',
                'description' => 'Kết hợp truy vấn SQL và trực quan hóa dữ liệu để tạo báo cáo kinh doanh.',
                'price' => 499000,
                'level' => Course::LEVEL_BEGINNER,
            ],
            [
                'title' => 'UI/UX thực chiến với Figma',
                'slug' => 'ui-ux-thuc-chien-voi-figma',
                'category' => 'thiet-ke-do-hoa-ui-ux',
                'description' => 'Nghiên cứu hành trình người dùng và thiết kế prototype giao diện bằng Figma.',
                'price' => 399000,
                'level' => Course::LEVEL_BEGINNER,
            ],
            [
                'title' => 'Thiết kế đồ họa số với Photoshop',
                'slug' => 'thiet-ke-do-hoa-so-voi-photoshop',
                'category' => 'thiet-ke-do-hoa-ui-ux',
                'description' => 'Thực hành chỉnh sửa ảnh, bố cục và tạo ấn phẩm truyền thông kỹ thuật số.',
                'price' => 349000,
                'level' => Course::LEVEL_BEGINNER,
            ],
            [
                'title' => 'Xây dựng Design System cho sản phẩm số',
                'slug' => 'xay-dung-design-system-cho-san-pham-so',
                'category' => 'thiet-ke-do-hoa-ui-ux',
                'description' => 'Tổ chức màu sắc, kiểu chữ và component để thiết kế sản phẩm nhất quán.',
                'price' => 499000,
                'level' => Course::LEVEL_INTERMEDIATE,
            ],
            [
                'title' => 'Digital Marketing đa kênh cho người mới',
                'slug' => 'digital-marketing-da-kenh-cho-nguoi-moi',
                'category' => 'marketing-kinh-doanh',
                'description' => 'Lập kế hoạch tiếp thị số và phối hợp nội dung trên các kênh trực tuyến.',
                'price' => 449000,
                'level' => Course::LEVEL_BEGINNER,
            ],
            [
                'title' => 'Content Marketing và kể chuyện thương hiệu',
                'slug' => 'content-marketing-va-ke-chuyen-thuong-hieu',
                'category' => 'marketing-kinh-doanh',
                'description' => 'Xây dựng thông điệp, lịch nội dung và câu chuyện thương hiệu thu hút khách hàng.',
                'price' => 549000,
                'level' => Course::LEVEL_INTERMEDIATE,
            ],
            [
                'title' => 'Excel ứng dụng trong phân tích kinh doanh',
                'slug' => 'excel-ung-dung-trong-phan-tich-kinh-doanh',
                'category' => 'marketing-kinh-doanh',
                'description' => 'Sử dụng công thức, bảng tổng hợp và biểu đồ để phân tích dữ liệu kinh doanh.',
                'price' => 299000,
                'level' => Course::LEVEL_BEGINNER,
            ],
            [
                'title' => 'Tiếng Anh giao tiếp trong môi trường công sở',
                'slug' => 'tieng-anh-giao-tiep-trong-moi-truong-cong-so',
                'category' => 'ngoai-ngu',
                'description' => 'Luyện các tình huống giao tiếp tiếng Anh thường gặp trong công việc.',
                'price' => 0,
                'level' => Course::LEVEL_BEGINNER,
            ],
            [
                'title' => 'IELTS nền tảng: chiến lược và kỹ năng học',
                'slug' => 'ielts-nen-tang-chien-luoc-va-ky-nang-hoc',
                'category' => 'ngoai-ngu',
                'description' => 'Làm quen với cấu trúc bài thi IELTS và xây dựng lộ trình luyện tập bốn kỹ năng.',
                'price' => 699000,
                'level' => Course::LEVEL_INTERMEDIATE,
            ],
            [
                'title' => 'Quản lý thời gian và học tập hiệu quả',
                'slug' => 'quan-ly-thoi-gian-va-hoc-tap-hieu-qua',
                'category' => 'ky-nang-ca-nhan',
                'description' => 'Lập kế hoạch học tập, ưu tiên công việc và duy trì thói quen làm việc bền vững.',
                'price' => 199000,
                'level' => Course::LEVEL_BEGINNER,
            ],
        ];

        foreach ($courseDefinitions as $index => $courseDefinition) {
            $instructorEmail = $instructorEmails[$index % count($instructorEmails)];
            $instructor = $instructors->get($instructorEmail);

            if (! $instructor instanceof User) {
                throw new RuntimeException("Demo instructor [{$instructorEmail}] was not found.");
            }

            $course = Course::updateOrCreate(
                ['slug' => $courseDefinition['slug']],
                [
                    'instructor_id' => $instructor->id,
                    'category_id' => $categoryIds[$courseDefinition['category']],
                    'title' => $courseDefinition['title'],
                    'description' => $courseDefinition['description'],
                    'price' => $courseDefinition['price'],
                    'level' => $courseDefinition['level'],
                    'thumbnail' => null,
                    'status' => Course::STATUS_PUBLISHED,
                    'reject_reason' => null,
                ],
            );

            $chapter = Chapter::updateOrCreate(
                ['course_id' => $course->id, 'order' => 1],
                ['title' => 'Lộ trình học và thực hành'],
            );

            foreach ([
                [
                    'title' => "Kiến thức nền tảng: {$course->title}",
                    'content' => "<p>Giới thiệu các khái niệm cốt lõi và công cụ cần chuẩn bị cho khóa học {$course->title}.</p>",
                ],
                [
                    'title' => "Bài tập thực hành: {$course->title}",
                    'content' => "<p>Áp dụng kiến thức đã học vào bài tập thực hành tổng hợp của khóa học {$course->title}.</p>",
                ],
            ] as $lessonIndex => $lessonDefinition) {
                Lesson::updateOrCreate(
                    ['chapter_id' => $chapter->id, 'order' => $lessonIndex + 1],
                    $lessonDefinition + ['video_url' => null, 'attachment_path' => null],
                );
            }
        }
    }
}
