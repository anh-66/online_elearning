<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonComment;
use App\Models\LessonProgress;
use App\Models\Notification;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ==========================================
        // 1. TÀI KHOẢN NGƯỜI DÙNG (USERS)
        // ==========================================

        // 1 Tài khoản Admin
        $admin = User::updateOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'name' => 'Quản Trị Viên Hệ Thống',
            'password' => '12345678',
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
        ]);

        // 2 Tài khoản Giảng viên
        $instructor1 = User::updateOrCreate([
            'email' => 'instructor1@gmail.com',
        ], [
            'name' => 'ThS. Nguyễn Văn Giảng',
            'password' => '12345678',
            'role' => User::ROLE_INSTRUCTOR,
            'bio' => 'Thạc sĩ Khoa học Máy tính, chuyên gia Fullstack Web & Cloud Computing với hơn 8 năm kinh nghiệm giảng dạy tại các trường đại học lớn.',
            'status' => User::STATUS_ACTIVE,
        ]);

        // Tạo alias instructor@gmail.com để tương thích các test cũ
        User::updateOrCreate([
            'email' => 'instructor@gmail.com',
        ], [
            'name' => 'ThS. Nguyễn Văn Giảng',
            'password' => '12345678',
            'role' => User::ROLE_INSTRUCTOR,
            'bio' => 'Thạc sĩ Khoa học Máy tính, chuyên gia Fullstack Web & Cloud Computing.',
            'status' => User::STATUS_ACTIVE,
        ]);

        $instructor2 = User::updateOrCreate([
            'email' => 'instructor2@gmail.com',
        ], [
            'name' => 'TS. Trần Thị Minh',
            'password' => '12345678',
            'role' => User::ROLE_INSTRUCTOR,
            'bio' => 'Tiến sĩ Trí tuệ Nhân tạo & Khoa học Dữ liệu, tác giả nhiều công trình nghiên cứu và khóa học lập trình thực chiến.',
            'status' => User::STATUS_ACTIVE,
        ]);

        // 3 Tài khoản Học viên
        $student1 = User::updateOrCreate([
            'email' => 'student1@gmail.com',
        ], [
            'name' => 'Lê Hoàng Học Viên',
            'password' => '12345678',
            'role' => User::ROLE_STUDENT,
            'status' => User::STATUS_ACTIVE,
        ]);

        User::updateOrCreate([
            'email' => 'student@gmail.com',
        ], [
            'name' => 'Lê Hoàng Học Viên',
            'password' => '12345678',
            'role' => User::ROLE_STUDENT,
            'status' => User::STATUS_ACTIVE,
        ]);

        $student2 = User::updateOrCreate([
            'email' => 'student2@gmail.com',
        ], [
            'name' => 'Phạm Minh Anh',
            'password' => '12345678',
            'role' => User::ROLE_STUDENT,
            'status' => User::STATUS_ACTIVE,
        ]);

        $student3 = User::updateOrCreate([
            'email' => 'student3@gmail.com',
        ], [
            'name' => 'Võ Quốc Huy',
            'password' => '12345678',
            'role' => User::ROLE_STUDENT,
            'status' => User::STATUS_ACTIVE,
        ]);

        // ==========================================
        // 2. DANH MỤC KHÓA HỌC (CATEGORIES)
        // ==========================================

        $catWeb = Category::updateOrCreate([
            'slug' => 'lap-trinh-web',
        ], [
            'name' => 'Lập trình Web',
            'icon' => 'code',
            'image' => null,
            'description' => 'Các khóa học về HTML/CSS, JavaScript, PHP, Laravel, React, Vue...',
        ]);

        $catAI = Category::updateOrCreate([
            'slug' => 'khoa-hoc-du-lieu-ai',
        ], [
            'name' => 'Khoa học Dữ liệu & AI',
            'icon' => 'chart-bar',
            'image' => null,
            'description' => 'Phân tích dữ liệu, Machine Learning, Deep Learning và Trí tuệ nhân tạo...',
        ]);

        $catDesign = Category::updateOrCreate([
            'slug' => 'thiet-ke-do-hoa-ui-ux',
        ], [
            'name' => 'Thiết kế Đồ họa & UI/UX',
            'icon' => 'palette',
            'image' => null,
            'description' => 'Thiết kế giao diện người dùng, Figma, Photoshop, trải nghiệm người dùng...',
        ]);

        // ==========================================
        // 3. KHÓA HỌC 1 (MIỄN PHÍ - LẬP TRÌNH WEB)
        // ==========================================

        $course1 = Course::updateOrCreate([
            'slug' => 'lap-trinh-web-fullstack-voi-laravel-va-tailwind-css',
        ], [
            'instructor_id' => $instructor1->id,
            'category_id' => $catWeb->id,
            'title' => 'Lập trình Web Fullstack với Laravel và Tailwind CSS',
            'description' => '<p>Khóa học toàn diện từ con số 0 giúp bạn làm chủ <strong>Laravel Framework</strong> kết hợp với <strong>Tailwind CSS</strong> hiện đại. Khóa học bao gồm kiến thức nền tảng, xây dựng dự án thực tế, xác thực người dùng và bảo mật hệ thống.</p>',
            'price' => 0.00,
            'level' => Course::LEVEL_BEGINNER,
            'thumbnail' => null,
            'status' => Course::STATUS_PUBLISHED,
            'reject_reason' => null,
        ]);

        // Chương 1 - Khóa 1
        $ch1_c1 = Chapter::updateOrCreate([
            'course_id' => $course1->id,
            'order' => 1,
        ], [
            'title' => 'Chương 1: Tổng quan và Cài đặt môi trường',
        ]);

        $lesson1 = Lesson::updateOrCreate([
            'chapter_id' => $ch1_c1->id,
            'order' => 1,
        ], [
            'title' => 'Bài 1: Giới thiệu kiến trúc MVC trong Laravel',
            'content' => '<p>Trong bài học đầu tiên, chúng ta sẽ tìm hiểu về mô hình <strong>Model - View - Controller (MVC)</strong> và luồng xử lý Request trong Laravel Framework.</p>',
            'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY',
            'attachment_path' => null,
        ]);

        $lesson2 = Lesson::updateOrCreate([
            'chapter_id' => $ch1_c1->id,
            'order' => 2,
        ], [
            'title' => 'Bài 2: Hướng dẫn cài đặt Composer và khởi tạo dự án',
            'content' => '<p>Hướng dẫn chi tiết cài đặt PHP 8.3, Composer và khởi tạo dự án Laravel đầu tiên thông qua command line.</p>',
            'video_url' => 'https://www.youtube.com/watch?v=ImtZ5yENzgE',
            'attachment_path' => null,
        ]);

        // Chương 2 - Khóa 1
        $ch2_c1 = Chapter::updateOrCreate([
            'course_id' => $course1->id,
            'order' => 2,
        ], [
            'title' => 'Chương 2: Xây dựng Cơ sở dữ liệu và Eloquent ORM',
        ]);

        $lesson3 = Lesson::updateOrCreate([
            'chapter_id' => $ch2_c1->id,
            'order' => 1,
        ], [
            'title' => 'Bài 3: Migration và Seeder trong Laravel',
            'content' => '<p>Tìm hiểu cách định nghĩa cấu trúc bảng thông qua Migration và nạp dữ liệu mẫu nhanh chóng bằng Seeder.</p>',
            'video_url' => 'https://www.youtube.com/watch?v=MYyJ4PuL4pY',
            'attachment_path' => null,
        ]);

        $lesson4 = Lesson::updateOrCreate([
            'chapter_id' => $ch2_c1->id,
            'order' => 2,
        ], [
            'title' => 'Bài 4: Các mối quan hệ Eloquent Relationship cơ bản',
            'content' => '<p>Thực hành liên kết dữ liệu với HasMany, BelongsTo, BelongsToMany và HasManyThrough.</p>',
            'video_url' => 'https://www.youtube.com/watch?v=ImtZ5yENzgE',
            'attachment_path' => null,
        ]);

        // Quiz Khóa 1 (Trắc nghiệm cuối chương 2)
        $quiz1 = Quiz::updateOrCreate([
            'course_id' => $course1->id,
            'chapter_id' => $ch2_c1->id,
        ], [
            'title' => 'Trắc nghiệm Kiểm tra Kiến thức Laravel & Eloquent',
            'description' => 'Bài kiểm tra trắc nghiệm 15 phút đánh giá mức độ hiểu biết về Migration và Eloquent Relationship.',
        ]);

        // Câu hỏi 1 - Quiz 1
        $q1 = Question::updateOrCreate([
            'quiz_id' => $quiz1->id,
            'question_text' => 'Lệnh Artisan nào dùng để làm mới toàn bộ database và tự động chạy Seeder?',
        ], [
            'explanation' => 'Lệnh migrate:fresh sẽ drop toàn bộ bảng rồi chạy lại migration, cờ --seed sẽ gọi DatabaseSeeder.',
        ]);

        Answer::updateOrCreate(['question_id' => $q1->id, 'answer_text' => 'php artisan migrate:fresh --seed'], ['is_correct' => true]);
        Answer::updateOrCreate(['question_id' => $q1->id, 'answer_text' => 'php artisan make:migration --seed'], ['is_correct' => false]);
        Answer::updateOrCreate(['question_id' => $q1->id, 'answer_text' => 'php artisan db:seed --all'], ['is_correct' => false]);
        Answer::updateOrCreate(['question_id' => $q1->id, 'answer_text' => 'php artisan migrate:run'], ['is_correct' => false]);

        // Câu hỏi 2 - Quiz 1
        $q2 = Question::updateOrCreate([
            'quiz_id' => $quiz1->id,
            'question_text' => 'Trong Eloquent ORM, quan hệ 1-Nhiều được định nghĩa bằng phương thức nào trên Model cha?',
        ], [
            'explanation' => 'Model cha chứa phương thức hasMany() để trỏ tới các bản ghi con.',
        ]);

        Answer::updateOrCreate(['question_id' => $q2->id, 'answer_text' => 'hasMany'], ['is_correct' => true]);
        Answer::updateOrCreate(['question_id' => $q2->id, 'answer_text' => 'belongsTo'], ['is_correct' => false]);
        Answer::updateOrCreate(['question_id' => $q2->id, 'answer_text' => 'hasOne'], ['is_correct' => false]);
        Answer::updateOrCreate(['question_id' => $q2->id, 'answer_text' => 'belongsToMany'], ['is_correct' => false]);

        // Ghi danh (Enrollment) Khóa 1
        Enrollment::updateOrCreate([
            'user_id' => $student1->id,
            'course_id' => $course1->id,
        ], [
            'status' => Enrollment::STATUS_FREE,
            'enrolled_at' => now()->subDays(5),
        ]);

        Enrollment::updateOrCreate([
            'user_id' => $student2->id,
            'course_id' => $course1->id,
        ], [
            'status' => Enrollment::STATUS_FREE,
            'enrolled_at' => now()->subDays(3),
        ]);

        // Tiến độ học (LessonProgress) cho Student 1
        LessonProgress::updateOrCreate([
            'user_id' => $student1->id,
            'lesson_id' => $lesson1->id,
        ], [
            'is_completed' => true,
            'completed_at' => now()->subDays(4),
        ]);

        LessonProgress::updateOrCreate([
            'user_id' => $student1->id,
            'lesson_id' => $lesson2->id,
        ], [
            'is_completed' => true,
            'completed_at' => now()->subDays(3),
        ]);

        // Đánh giá (Reviews) Khóa 1
        Review::updateOrCreate([
            'user_id' => $student1->id,
            'course_id' => $course1->id,
        ], [
            'rating' => 5,
            'comment' => 'Khóa học rất chi tiết và dễ hiểu dành cho người mới bắt đầu! Cảm ơn thầy nhiều ạ.',
        ]);

        Review::updateOrCreate([
            'user_id' => $student2->id,
            'course_id' => $course1->id,
        ], [
            'rating' => 4,
            'comment' => 'Nội dung thực tế, bài giảng chất lượng cao, mong thầy ra thêm các phần nâng cao về API.',
        ]);

        // Bình luận/Hỏi đáp (Lesson Comments) dưới Bài 1
        $comment1 = LessonComment::updateOrCreate([
            'user_id' => $student1->id,
            'lesson_id' => $lesson1->id,
            'parent_id' => null,
        ], [
            'content' => 'Thưa thầy, em cài Composer trên Windows bị báo lỗi biến môi trường PHP thì khắc phục thế nào ạ?',
        ]);

        LessonComment::updateOrCreate([
            'user_id' => $instructor1->id,
            'lesson_id' => $lesson1->id,
            'parent_id' => $comment1->id,
        ], [
            'content' => 'Chào em, em hãy kiểm tra lại biến PATH trong Environment Variables của Windows xem đã có đường dẫn tới thư mục PHP chưa nhé!',
        ]);

        // ==========================================
        // 4. KHÓA HỌC 2 (TRẢ PHÍ - KHOA HỌC DỮ LIỆU & AI)
        // ==========================================

        $course2 = Course::updateOrCreate([
            'slug' => 'xay-dung-ung-dung-ai-va-phan-tich-du-lieu-thuc-chien-voi-python',
        ], [
            'instructor_id' => $instructor2->id,
            'category_id' => $catAI->id,
            'title' => 'Xây dựng Ứng dụng AI và Phân tích Dữ liệu Thực chiến với Python',
            'description' => '<p>Khóa học trang bị cho bạn tư duy phân tích dữ liệu, làm chủ các thư viện <strong>Pandas, NumPy, Scikit-Learn</strong> và cách tích hợp mô hình AI vào ứng dụng web thông qua RESTful API.</p>',
            'price' => 799000.00,
            'level' => Course::LEVEL_INTERMEDIATE,
            'thumbnail' => null,
            'status' => Course::STATUS_PUBLISHED,
            'reject_reason' => null,
        ]);

        // Chương 1 - Khóa 2
        $ch1_c2 = Chapter::updateOrCreate([
            'course_id' => $course2->id,
            'order' => 1,
        ], [
            'title' => 'Chương 1: Thu thập và Tiền xử lý Dữ liệu',
        ]);

        $l1_c2 = Lesson::updateOrCreate([
            'chapter_id' => $ch1_c2->id,
            'order' => 1,
        ], [
            'title' => 'Bài 1: Làm sạch dữ liệu với thư viện Pandas và NumPy',
            'content' => '<p>Kỹ thuật xử lý missing values, chuẩn hóa dữ liệu số và lọc dữ liệu dạng bảng lớn.</p>',
            'video_url' => 'https://www.youtube.com/watch?v=vmEHCJofslg',
            'attachment_path' => null,
        ]);

        $l2_c2 = Lesson::updateOrCreate([
            'chapter_id' => $ch1_c2->id,
            'order' => 2,
        ], [
            'title' => 'Bài 2: Trực quan hóa dữ liệu cùng Matplotlib & Seaborn',
            'content' => '<p>Thực hành vẽ biểu đồ phân phối, ma trận tương quan giữa các đặc trưng (features correlation).</p>',
            'video_url' => 'https://www.youtube.com/watch?v=a9UrKTVEeZA',
            'attachment_path' => null,
        ]);

        // Chương 2 - Khóa 2
        $ch2_c2 = Chapter::updateOrCreate([
            'course_id' => $course2->id,
            'order' => 2,
        ], [
            'title' => 'Chương 2: Huấn luyện Mô hình Học máy Cơ bản',
        ]);

        $l3_c2 = Lesson::updateOrCreate([
            'chapter_id' => $ch2_c2->id,
            'order' => 1,
        ], [
            'title' => 'Bài 3: Hồi quy tuyến tính và Phân loại với Scikit-Learn',
            'content' => '<p>Xây dựng bài toán dự đoán giá nhà (Regression) và phân loại email spam (Classification).</p>',
            'video_url' => 'https://www.youtube.com/watch?v=0B5eIE_1vpU',
            'attachment_path' => null,
        ]);

        // Quiz Khóa 2
        $quiz2 = Quiz::updateOrCreate([
            'course_id' => $course2->id,
            'chapter_id' => $ch1_c2->id,
        ], [
            'title' => 'Trắc nghiệm Tiền xử lý Dữ liệu với Pandas',
            'description' => 'Kiểm tra kỹ năng thao tác DataFrame, lọc và xử lý dữ liệu bị khuyết.',
        ]);

        $q1_c2 = Question::updateOrCreate([
            'quiz_id' => $quiz2->id,
            'question_text' => 'Hàm nào trong thư viện Pandas dùng để kiểm tra các giá trị bị khuyết (NaN) trong DataFrame?',
        ], [
            'explanation' => 'Phương thức isnull() hoặc isna() trả về một DataFrame chứa các giá trị boolean tương ứng.',
        ]);

        Answer::updateOrCreate(['question_id' => $q1_c2->id, 'answer_text' => 'df.isnull()'], ['is_correct' => true]);
        Answer::updateOrCreate(['question_id' => $q1_c2->id, 'answer_text' => 'df.dropna()'], ['is_correct' => false]);
        Answer::updateOrCreate(['question_id' => $q1_c2->id, 'answer_text' => 'df.empty()'], ['is_correct' => false]);
        Answer::updateOrCreate(['question_id' => $q1_c2->id, 'answer_text' => 'df.check_null()'], ['is_correct' => false]);

        // Ghi danh Khóa 2
        Enrollment::updateOrCreate([
            'user_id' => $student3->id,
            'course_id' => $course2->id,
        ], [
            'status' => Enrollment::STATUS_PAID,
            'enrolled_at' => now()->subDay(),
        ]);

        // Đánh giá Khóa 2
        Review::updateOrCreate([
            'user_id' => $student3->id,
            'course_id' => $course2->id,
        ], [
            'rating' => 5,
            'comment' => 'Kiến thức rất thực chiến, các ví dụ trực quan và code mẫu chạy mượt mà.',
        ]);

        // ==========================================
        // 5. BANNER TRANG CHỦ & THÔNG BÁO HỆ THỐNG
        // ==========================================

        Banner::updateOrCreate([
            'title' => 'Nâng tầm kỹ năng lập trình cùng chuyên gia',
        ], [
            'image_path' => 'banners/banner-1.jpg',
            'link_url' => '/courses',
            'order' => 1,
            'is_active' => true,
        ]);

        Banner::updateOrCreate([
            'title' => 'Khóa học mới: Làm chủ Trí tuệ Nhân tạo 2026',
        ], [
            'image_path' => 'banners/banner-2.jpg',
            'link_url' => '/courses/xay-dung-ung-dung-ai-va-phan-tich-du-lieu-thuc-chien-voi-python',
            'order' => 2,
            'is_active' => true,
        ]);

        Notification::updateOrCreate([
            'title' => 'Chào mừng bạn đến với Nền tảng Học trực tuyến!',
        ], [
            'content' => 'Hệ thống đã chính thức đi vào hoạt động với nhiều khóa học công nghệ miễn phí và hấp dẫn.',
            'type' => 'system',
        ]);

        Notification::updateOrCreate([
            'title' => 'Lịch bảo trì nâng cấp hệ thống máy chủ',
        ], [
            'content' => 'Hệ thống sẽ tiến hành bảo trì định kỳ từ 02:00 đến 04:00 ngày Chủ Nhật tới. Rất mong quý học viên thông cảm.',
            'type' => 'system',
        ]);
    }
}
