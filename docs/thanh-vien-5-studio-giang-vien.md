# Thành viên 5 — Module Studio Giảng viên & Quản lý nội dung (Instructor Dashboard)

> Nhánh làm việc: `feature/instructor-studio` (tách từ `main`, không chỉnh sửa trực tiếp trên `main`).

## 1. Mục tiêu

Xây dựng không gian riêng cho Giảng viên tại `/instructor/*` để:

- Tự tạo, chỉnh sửa, xóa khóa học (upload ảnh đại diện + nhiều ảnh thư viện).
- Soạn mô tả khóa học và nội dung bài học bằng **CKEditor 5** (chèn ảnh, bảng, video).
- Quản lý chương / bài học (thêm, sửa, xóa, sắp xếp thứ tự).
- Quản lý quiz, câu hỏi trắc nghiệm và đáp án.
- Gửi khóa học cho Admin duyệt (`draft → pending`).
- Theo dõi học viên đã ghi danh, tiến độ từng người và điểm đánh giá trung bình.

## 2. Danh sách chức năng đã làm

### 2.1. Dashboard giảng viên (`/instructor/dashboard`)
- 4 thẻ thống kê: tổng số khóa học (kèm số đã xuất bản / chờ duyệt), số học viên (không trùng), tổng lượt ghi danh, điểm đánh giá trung bình + số lượt đánh giá.
- Biểu đồ cột (Chart.js) lượt ghi danh 6 tháng gần nhất.
- Danh sách ghi danh gần đây, đánh giá mới nhất.
- Bảng thống kê theo từng khóa: trạng thái, số học viên, điểm đánh giá.

### 2.2. Khóa học của tôi (`/instructor/courses`)
- Tab lọc theo trạng thái: Tất cả / Bản nháp / Chờ duyệt / Đã xuất bản / Bị từ chối (kèm số lượng).
- Tìm kiếm theo tên khóa học, phân trang.
- Hiển thị ảnh, danh mục, trình độ, số chương/bài, số học viên, điểm đánh giá, học phí, lý do bị từ chối.

### 2.3. Form tạo / sửa khóa học
- Trường: tên, danh mục, trình độ, học phí (0 = miễn phí), mô tả (CKEditor 5).
- Upload **ảnh đại diện** (xem trước ngay khi chọn) và **nhiều ảnh thư viện** cùng lúc (tối đa 10 ảnh/lần, 5MB/ảnh).
- Xóa từng ảnh trong thư viện.
- Khóa học mới luôn ở trạng thái **draft**, slug tự sinh từ tên (hỗ trợ tiếng Việt, tự thêm `-2`, `-3`… nếu trùng).
- Khóa đã publish thì giữ nguyên slug khi đổi tên để không hỏng link công khai.

### 2.4. Quản lý nội dung khóa học (`/instructor/courses/{id}`)
- Đề cương: danh sách chương → bài học → quiz cuối chương.
- Thêm chương, đổi tên chương ngay tại chỗ, xóa chương (xóa luôn bài học và file đính kèm).
- Nút ↑ ↓ để sắp xếp thứ tự chương và bài học.
- Nút **Gửi duyệt**, **Rút lại yêu cầu duyệt**, **Xem trang công khai** (khi đã publish).

### 2.5. Bài học
- Tên bài, nội dung (CKEditor 5), link video YouTube (có xem trước video), tài liệu đính kèm (PDF, Word, PowerPoint, Excel, ZIP/RAR, TXT — tối đa 20MB).
- Khi sửa: có thể chuyển bài sang chương khác của cùng khóa, thay hoặc xóa tài liệu đính kèm.

### 2.6. Quiz & câu hỏi trắc nghiệm
- Tạo quiz gắn với một chương (quiz cuối chương) hoặc quiz chung của khóa.
- Trang quản lý quiz: danh sách câu hỏi, đáp án đúng được tô xanh, số lượt làm bài.
- Thêm / sửa câu hỏi: 2–6 đáp án (thêm/bớt động bằng Alpine.js), chọn 1 đáp án đúng, giải thích đáp án.

### 2.7. Quản lý học viên (`/instructor/students`)
- Lọc theo khóa học, tìm theo tên/email học viên, phân trang.
- Mỗi dòng hiển thị: học viên, khóa học, ngày ghi danh, hình thức (miễn phí/trả phí), **thanh tiến độ %** (số bài đã hoàn thành / tổng số bài).

## 3. Quy tắc nghiệp vụ

| Quy tắc | Cách xử lý |
|---|---|
| Giảng viên chỉ thao tác khóa học của mình | `App\Policies\CoursePolicy` (admin được thao tác mọi khóa) |
| Vòng đời trạng thái | `draft` / `rejected` → **Gửi duyệt** → `pending` → (Admin – TV6) `published` / `rejected` |
| Khóa đang `pending` | Bị khóa chỉnh sửa; giảng viên có thể **Rút lại** về `draft` |
| Điều kiện gửi duyệt | Khóa học phải có ít nhất 1 chương và 1 bài học |
| Gửi duyệt lại khóa bị từ chối | Xóa `reject_reason` cũ, chuyển sang `pending` |
| Xóa khóa học | Không cho xóa khi đã có học viên ghi danh; khi xóa sẽ dọn file ảnh và tài liệu |
| Video bài học | Bắt buộc là link YouTube (`youtube.com` / `youtu.be`) |
| Chuyển bài học sang chương khác | Chỉ được chọn chương thuộc cùng khóa học |

## 4. Danh sách file

### Backend
| File | Vai trò |
|---|---|
| `app/Http/Controllers/Instructor/DashboardController.php` | Thống kê dashboard giảng viên |
| `app/Http/Controllers/Instructor/CourseController.php` | CRUD khóa học, upload ảnh, gửi duyệt, rút lại, xóa ảnh |
| `app/Http/Controllers/Instructor/ChapterController.php` | CRUD chương, sắp xếp thứ tự |
| `app/Http/Controllers/Instructor/LessonController.php` | CRUD bài học, file đính kèm, sắp xếp thứ tự |
| `app/Http/Controllers/Instructor/QuizController.php` | CRUD quiz |
| `app/Http/Controllers/Instructor/QuestionController.php` | CRUD câu hỏi + đáp án |
| `app/Http/Controllers/Instructor/StudentController.php` | Danh sách học viên + tính tiến độ |
| `app/Http/Controllers/Instructor/Concerns/ReordersSiblings.php` | Trait dùng chung cho nút ↑ ↓ |
| `app/Http/Requests/Instructor/CourseRequest.php` | Validate khóa học |
| `app/Http/Requests/Instructor/LessonRequest.php` | Validate bài học |
| `app/Http/Requests/Instructor/QuestionRequest.php` | Validate câu hỏi trắc nghiệm |
| `app/Policies/CoursePolicy.php` | Phân quyền sở hữu khóa học |
| `routes/instructor.php` | Khai báo toàn bộ route `/instructor/*` |

### Frontend
| File | Trang |
|---|---|
| `resources/views/instructor/dashboard.blade.php` | Dashboard |
| `resources/views/instructor/courses/index.blade.php` | Khóa học của tôi |
| `resources/views/instructor/courses/create.blade.php`, `edit.blade.php`, `partials/form.blade.php` | Form tạo / sửa khóa học |
| `resources/views/instructor/courses/show.blade.php` | Quản lý chương / bài học / quiz |
| `resources/views/instructor/lessons/create.blade.php`, `edit.blade.php`, `partials/form.blade.php` | Form bài học |
| `resources/views/instructor/quizzes/create.blade.php`, `edit.blade.php`, `show.blade.php`, `partials/form.blade.php` | Quiz |
| `resources/views/instructor/questions/edit.blade.php`, `partials/form.blade.php` | Câu hỏi trắc nghiệm |
| `resources/views/instructor/students/index.blade.php` | Danh sách học viên theo khóa |
| `resources/views/instructor/partials/status-badge.blade.php` | Badge trạng thái khóa học |
| `resources/js/editor.js`, `resources/css/editor.css` | Cấu hình CKEditor 5 |

### File dùng chung có chỉnh sửa nhỏ
- `resources/views/layouts/instructor.blade.php`: link sidebar dùng `route()`, menu "Khóa học của tôi" sáng khi đang ở trang chương/bài học/quiz.
- `vite.config.js`: thêm entry `resources/js/editor.js`.
- `resources/css/app.css`: thêm style `[x-cloak]` cho Alpine.js.

### Test
- `tests/Feature/Instructor/InstructorStudioTest.php`

## 5. Bảng route

| Method | URL | Tên route | Chức năng |
|---|---|---|---|
| GET | `/instructor/dashboard` | `instructor.dashboard` | Dashboard |
| GET | `/instructor/courses` | `instructor.courses.index` | Khóa học của tôi |
| GET/POST | `/instructor/courses/create`, `/instructor/courses` | `instructor.courses.create` / `.store` | Tạo khóa học |
| GET | `/instructor/courses/{course}` | `instructor.courses.show` | Quản lý nội dung |
| GET/PUT | `/instructor/courses/{course}/edit`, `/instructor/courses/{course}` | `instructor.courses.edit` / `.update` | Sửa khóa học |
| DELETE | `/instructor/courses/{course}` | `instructor.courses.destroy` | Xóa khóa học |
| POST | `/instructor/courses/{course}/submit` | `instructor.courses.submit` | Gửi duyệt |
| POST | `/instructor/courses/{course}/withdraw` | `instructor.courses.withdraw` | Rút lại yêu cầu duyệt |
| DELETE | `/instructor/courses/{course}/images/{image}` | `instructor.courses.images.destroy` | Xóa ảnh thư viện |
| POST | `/instructor/courses/{course}/chapters` | `instructor.courses.chapters.store` | Thêm chương |
| PUT/DELETE | `/instructor/chapters/{chapter}` | `instructor.chapters.update` / `.destroy` | Sửa / xóa chương |
| PATCH | `/instructor/chapters/{chapter}/move` | `instructor.chapters.move` | Đổi thứ tự chương |
| GET/POST | `/instructor/chapters/{chapter}/lessons/create`, `/instructor/chapters/{chapter}/lessons` | `instructor.chapters.lessons.create` / `.store` | Thêm bài học |
| GET/PUT/DELETE | `/instructor/lessons/{lesson}(/edit)` | `instructor.lessons.edit` / `.update` / `.destroy` | Sửa / xóa bài học |
| PATCH | `/instructor/lessons/{lesson}/move` | `instructor.lessons.move` | Đổi thứ tự bài học |
| GET/POST | `/instructor/courses/{course}/quizzes/create`, `/instructor/courses/{course}/quizzes` | `instructor.courses.quizzes.create` / `.store` | Tạo quiz |
| GET/PUT/DELETE | `/instructor/quizzes/{quiz}(/edit)` | `instructor.quizzes.show` / `.edit` / `.update` / `.destroy` | Quản lý quiz |
| POST | `/instructor/quizzes/{quiz}/questions` | `instructor.quizzes.questions.store` | Thêm câu hỏi |
| GET/PUT/DELETE | `/instructor/questions/{question}(/edit)` | `instructor.questions.edit` / `.update` / `.destroy` | Sửa / xóa câu hỏi |
| GET | `/instructor/students` | `instructor.students.index` | Học viên theo khóa |

Tất cả route trên đã được bọc middleware `['web', 'auth', 'role:instructor,admin']` trong `bootstrap/app.php`.

## 6. Tích hợp CKEditor 5

- Dùng package `ckeditor5` (đã có trong `package.json`), cấu hình tại `resources/js/editor.js`.
- Mọi `<textarea data-editor data-upload-url="...">` sẽ tự động thành CKEditor.
- Ảnh chèn vào editor được upload qua route có sẵn `upload.image` (`UploadController`) → lưu vào `storage/app/public/editor`.
- Thanh công cụ: heading, in đậm/nghiêng/gạch chân, link, **chèn ảnh**, **chèn video (Media embed)**, **bảng**, trích dẫn, code block, căn lề, danh sách.
- Video nhúng được lưu thẳng dạng `<iframe>` (`previewsInData: true`) để trang học bài hiển thị được ngay.

## 7. Cách chạy & kiểm thử

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build        # hoặc npm run dev khi đang code
php artisan serve
```

Đăng nhập bằng tài khoản giảng viên mẫu trong seeder: `instructor1@gmail.com` / `12345678`, sau đó vào `/instructor/dashboard`.

Chạy test của module:

```bash
php artisan test --compact tests/Feature/Instructor/InstructorStudioTest.php
```

Các trường hợp được test: chặn học viên vào Studio, render toàn bộ trang, tạo khóa học kèm ảnh, slug không trùng, chặn sửa khóa của giảng viên khác, điều kiện gửi duyệt, khóa chỉnh sửa khi pending + rút lại, chặn xóa khóa đã có học viên, CRUD chương/bài học + sắp xếp, validate link YouTube, chặn chuyển bài sang khóa khác, CRUD quiz/câu hỏi/đáp án, tiến độ học viên và điểm đánh giá trung bình.

## 8. Ghi chú phối hợp với các thành viên khác

- **TV6 (Admin):** duyệt khóa `pending` → `published` hoặc `rejected` kèm `reject_reason`; Studio đã hiển thị lý do từ chối và cho gửi duyệt lại.
- **TV3 (Learning Player):** nội dung bài học là HTML từ CKEditor (có thể chứa `<iframe>` video, ảnh, bảng); `video_url` là link YouTube gốc; file đính kèm nằm ở `storage/lessons/attachments`.
- **TV4 (Quiz):** mỗi câu hỏi có đúng 1 đáp án `is_correct = true`, có thể dùng `$question->correctAnswer` để chấm điểm.
- **TV2 (Trang chi tiết khóa học):** ảnh thư viện lưu ở cột `course_images.image_path`; view `courses/show.blade.php` hiện đang đọc `$image->path` nên cần sửa thành `$image->image_path` để ảnh hiển thị.
