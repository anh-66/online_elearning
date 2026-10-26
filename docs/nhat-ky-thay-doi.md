# Nhật ký những việc Claude đã làm

Tài liệu này ghi lại các thay đổi Claude thực hiện trên nhánh `feature/demo`, theo thứ tự thời
gian. Nhánh `main` không bị động đến.

## 26/10/2026: bổ sung theo tài liệu phân tích thiết kế

**Tách khu giảng viên và khu quản trị**

- Giảng viên dùng `/instructor`: bảng điều khiển (số học viên, điểm đánh giá trung bình, biểu đồ
  ghi danh), khoá học của tôi, chương & bài học, quiz, danh sách học viên kèm % tiến độ.
- Quản trị viên dùng `/admin`: duyệt khoá học, danh mục, ghi danh, đánh giá, người dùng, thông báo.
  Giảng viên không vào được `/admin` và ngược lại.

**Chương (sections)**: mỗi khoá học chia thành chương, bài học thuộc một chương. Trang học và
trang chi tiết khoá học hiển thị nội dung theo chương.

**Quiz trắc nghiệm**: giảng viên tạo quiz cho từng chương, thêm câu hỏi (2 đến 6 đáp án, một đáp
án đúng), đặt điểm đạt. Học viên đã ghi danh làm bài, được chấm điểm tự động, xem lại đáp án và
lịch sử các lần làm.

**Duyệt khoá học**: khoá mới là bản nháp; giảng viên bấm "Gửi duyệt" (chờ duyệt); quản trị viên
duyệt (xuất bản), từ chối kèm lý do, hoặc ẩn. Khoá bị từ chối được sửa rồi gửi duyệt lại.

**Mật khẩu**: đăng ký, đăng nhập, đổi mật khẩu đều yêu cầu ít nhất 6 ký tự, 1 chữ in hoa và 1 ký
tự đặc biệt.

**Dữ liệu mẫu**: mỗi khoá có 3 chương, 6 bài, 3 quiz; có sẵn lượt làm quiz, một khoá chờ duyệt,
một bản nháp và một khoá bị từ chối của `teacher@gmail.com`. Mật khẩu mới: `Admin@123`,
`Teacher@123`, `Student@123`.

Cần chạy lại `php artisan migrate:fresh --seed` và `npm run build`.

## 24/10/2026: xây dựng trang web UTH E-Learning

Commit `a727e82` (*feat: build UTH e-learning site with admin panel and learning flow*).

**Trang người dùng**

- Trang chủ, thư viện khoá học (tìm kiếm, lọc theo danh mục, trình độ, học phí, sắp xếp,
  phân trang), chi tiết khoá học (thư viện ảnh, nội dung bài học, đánh giá), thông báo.
- Ghi danh và huỷ ghi danh, trang học bài có video YouTube, đánh dấu hoàn thành bài, thanh
  tiến độ.
- Góc học tập (`/dashboard`) với thống kê và % hoàn thành từng khoá.
- Hồ sơ cá nhân: thêm số điện thoại, giới thiệu, upload ảnh đại diện.

**Trang quản trị** (`/admin`)

- Bảng điều khiển có thẻ thống kê và biểu đồ Chart.js.
- CRUD khoá học (ảnh đại diện + nhiều ảnh), bài học, danh mục, người dùng, thông báo.
- Quản lý ghi danh, duyệt hoặc ẩn đánh giá.

**Nền tảng**

- Migration cho các bảng `categories`, `courses`, `course_images`, `lessons`, `enrollments`,
  `lesson_completions`, `reviews`, `announcements` và thêm cột hồ sơ vào `users`.
- Enum `UserRole`, `CourseLevel`, `CourseStatus`; middleware `role` (`EnsureUserHasRole`);
  `CoursePolicy`.
- Tích hợp CKEditor 5 kèm upload ảnh và bộ lọc `HtmlSanitizer` chống XSS.
- Việt hoá giao diện (`lang/vi`, `lang/vi.json`), giao diện màu xanh teal theo phong cách UTH,
  responsive cho điện thoại.
- Seeder dữ liệu mẫu: 6 danh mục, 12 khoá học, bài học, 25 học viên, ghi danh, đánh giá,
  thông báo.
- Factory và kiểm thử tính năng trong `tests/Feature` (`CourseManagementTest`, `LearningTest`,
  `PageRenderingTest`, ...).
- Tài liệu phân tích thiết kế [phan-tich-thiet-ke.md](phan-tich-thiet-ke.md) (use case, ERD).

## 26/10/2026: chuẩn bị deploy lên Railway

Commit `f849a4a` (*feat: add Docker deployment configuration and application documentation
screenshots*).

| File | Thay đổi |
| --- | --- |
| `Dockerfile` | Build 3 bước: cài thư viện PHP bằng Composer, build asset bằng Vite (Node 22), chạy bằng PHP 8.4 + Apache với các extension `pdo_mysql gd zip intl bcmath opcache exif` |
| `.dockerignore` | Bỏ `vendor`, `node_modules`, `.env`... khỏi image |
| `docker/start.sh` | Khi container khởi động: đổi cổng Apache theo biến `PORT`, tạo thư mục `storage`, chạy `storage:link`, `migrate --force`, seed nếu database trống, `optimize` |
| `railway.json` | Cho Railway build bằng `Dockerfile`, kiểm tra sức khoẻ ở `/up` |
| `bootstrap/app.php` | Thêm `trustProxies(at: '*')` để Laravel nhận đúng HTTPS khi chạy sau proxy của Railway |
| `routes/console.php` | Lệnh `php artisan app:seed-if-empty`: chỉ seed khi chưa có người dùng, tránh nhân đôi dữ liệu mỗi lần deploy |
| `composer.json`, `composer.lock` | Chuyển `fakerphp/faker` từ `require-dev` sang `require`, vì seeder dùng Faker trên server (môi trường production không cài gói dev) |
| `docs/deploy-railway.md` | Hướng dẫn deploy từng bước trên Railway |
| `docs/screenshots/` | 12 ảnh chụp màn hình các trang, cả bản máy tính và điện thoại |

Lưu ý: từ phía Claude, lệnh push lên GitHub bị từ chối (lỗi 403), nên code được ghi thẳng vào
thư mục dự án trên máy và bạn tự commit, push.

## 26/10/2026: cài đặt để chạy trên máy (Windows)

- Bật `extension=gd` trong `php.ini` (cần cho xử lý ảnh và thư viện Excel/PDF).
- Tạo database MySQL `online_elearning`.
- Chạy `php artisan migrate --seed` để tạo bảng và dữ liệu mẫu.
- Tài khoản quản trị sau khi seed: `admin@gmail.com` / `12345678`.

## 26/10/2026: viết tài liệu

Thêm vào thư mục `docs/`:

- [README.md](README.md): mục lục tài liệu.
- [mo-ta-trang-web.md](mo-ta-trang-web.md): mô tả tính năng và từng trang.
- [nhat-ky-thay-doi.md](nhat-ky-thay-doi.md): file này.
- [huong-dan-chay.md](huong-dan-chay.md): hướng dẫn cài đặt và chạy code.
