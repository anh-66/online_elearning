# Rà soát Thành viên 1, 2, 5 & đổi tông màu giao diện (sáng / tối)

> Nhánh làm việc: `feature/review-member-1-2-5` (tách từ `main`, **không** chỉnh sửa gì trên `main`).
> Trạng thái: các thay đổi đang ở working tree, **chưa commit / chưa push**.

## 1. Kết quả rà soát chức năng

| Thành viên | Phạm vi | Kết quả |
|---|---|---|
| 1 | Xác thực & Hồ sơ (đăng ký, đăng nhập, Google, quên / đặt lại mật khẩu, xác minh email, hồ sơ, avatar, đổi mật khẩu, xóa tài khoản) | Đủ chức năng. Đã bổ sung trang **Bảng điều khiển học viên** và đưa trang hồ sơ về layout công khai. |
| 2 | Trang công khai & Danh mục (trang chủ, banner, danh sách khóa học, tìm kiếm / lọc / sắp xếp, chi tiết khóa học, hồ sơ giảng viên) | Đủ chức năng. Đã sửa lỗi truy vấn `HAVING` (lỗi trên SQLite) và lỗi N+1. |
| 5 | Studio Giảng viên (khóa học, chương, bài học, quiz, câu hỏi, gửi duyệt, học viên, thống kê) | Đủ chức năng (xem thêm `docs/thanh-vien-5-studio-giang-vien.md`). |

**Quyết định giữ nguyên:** trang đăng ký công khai chỉ tạo tài khoản **học viên**; tài khoản giảng viên do Admin tạo / cấp quyền. Đây là chủ ý, không phải thiếu sót.

## 2. Sửa lỗi & bổ sung logic

| File | Thay đổi |
|---|---|
| `routes/web.php` | Route `/dashboard` nhận `Request`, truyền `enrolledCourses` (6 khóa ghi danh gần nhất, kèm giảng viên, danh mục, đánh giá, số học viên) và `enrollmentTotal`. |
| `app/Http/Controllers/HomeController.php` | Thay `having('courses_count', '>', 0)` bằng `whereHas('courses', …published)`: `HAVING` không có `GROUP BY` lỗi trên SQLite. |
| `app/Http/Controllers/CourseCatalogController.php` | Sửa tương tự cho danh sách danh mục ở bộ lọc. |
| `app/Http/Controllers/InstructorProfileController.php` | Eager load thêm `instructor` cho card khóa học (tránh N+1). |
| `tests/Feature/ExampleTest.php` | Bật `RefreshDatabase` để test trang chủ chạy được trên DB trống. |
| `courses/show`, `instructor/dashboard` | Thời gian tương đối hiển thị tiếng Việt: `->locale('vi')->diffForHumans()`. |

## 3. Giao diện

### 3.1. Trang mới / được làm lại
- **`dashboard.blade.php`**: bảng điều khiển học viên gồm lời chào, số khóa đã ghi danh, các ô truy cập nhanh và lưới khóa học gần đây (có trạng thái rỗng).
- **`profile/edit.blade.php`** và các partial hồ sơ: chuyển sang `layouts.public`, đồng bộ màu với phần còn lại.

### 3.2. Đổi tông màu: bỏ nền đen, chuyển sang tông teal, có chế độ Sáng / Tối
Trước đây hero, sidebar và trang đăng nhập dùng nền **đen** (`bg-black`). Nay:

- **Màu nhấn `brand`** khai báo trong `tailwind.config.js` (`brand: colors.teal`, `brand-700 = #0f766e`), theo tông teal của cổng UTH. Mọi class `green-*` trong các view của thành viên 1, 2, 5 đã đổi sang `brand-*`. Muốn đổi màu cả dự án chỉ cần sửa một dòng này.
- Các class `emerald-*` **giữ nguyên** vì mang nghĩa "thành công" (đã xuất bản, đáp án đúng, miễn phí, thông báo OK).
- Font chữ: **Be Vietnam Pro** (hiển thị tiếng Việt đẹp), các layout chưa nạp font này sẽ dùng Figtree.
- **Chế độ sáng (mặc định):** nền `slate-50`, card trắng, màu nhấn teal.
- **Chế độ tối:** nền `neutral-950`, card `neutral-900`, chữ `neutral-100`, màu nhấn `brand-400`.

### 3.3. Trang chủ & layout công khai theo phong cách trang mẫu
- **Header:** thanh liên hệ (hotline, email) màu `brand-900` và thanh điều hướng `brand-700` dính trên đầu trang. Logo "EduLearn – Học mọi lúc, mọi nơi", ô tìm kiếm mờ, nút Đăng ký nền trắng.
- **Hero:** gradient teal, chữ trắng, từ khóa nổi bật màu vàng (`amber-300`), ô "Bạn muốn học gì hôm nay?" và 4 thẻ thống kê kiểu kính mờ.
- **Truy cập nhanh:** 4 thẻ trắng đè lên mép dưới hero, nhấc lên khi rê chuột.
- **Banner** do Admin quản lý: chuyển xuống dưới dạng khung bo góc.
- **Danh mục / Khóa học được yêu thích:** tiêu đề lớn kèm dòng phụ.
- **CTA cuối trang:** "Sẵn sàng bắt đầu hành trình học tập?".
- **Footer:** nền teal rất đậm (`brand-950`).
- **Card khóa học:** khóa chưa có ảnh dùng nền gradient nhiều màu theo danh mục; badge trình độ dạng viên thuốc; tên danh mục viết hoa; giá màu teal.
- Biểu đồ ở dashboard giảng viên đổi từ tím sang teal.

#### Cách hoạt động
1. `tailwind.config.js` bật `darkMode: "class"`: mọi class `dark:*` chỉ có hiệu lực khi thẻ `<html>` có class `dark`.
2. `resources/views/partials/theme-script.blade.php` (mới): script nhỏ đặt trong `<head>`, chạy **trước khi vẽ trang** để không bị nháy màu. Script đọc `localStorage.theme`; nếu chưa chọn thì theo cài đặt hệ điều hành (`prefers-color-scheme`).
3. `resources/views/components/theme-toggle.blade.php` (mới): nút mặt trăng / mặt trời (Alpine.js), bấm để đổi chế độ và lưu lựa chọn vào `localStorage`.
4. `resources/css/app.css`: thêm style tối cho nội dung rich-text (`.rich-content`), cho ô nhập / select / textarea / checkbox, và `color-scheme: dark`.
5. `resources/css/editor.css`: ghi đè biến màu `--ck-color-*` để **CKEditor 5** cũng có giao diện tối.

#### Nút đổi giao diện nằm ở đâu
- Header trang công khai (`layouts/public`), cạnh nút đăng nhập / đăng ký.
- Góc trên phải các trang xác thực (`layouts/guest`).
- Header Studio giảng viên (`layouts/instructor`), cạnh link "Hồ sơ".

#### Phạm vi áp dụng
Đã áp dụng: trang chủ, danh sách / chi tiết khóa học, hồ sơ giảng viên, bảng điều khiển học viên, hồ sơ cá nhân, toàn bộ trang xác thực, các component Breeze, Studio giảng viên, trang lỗi 403 / 404.

**Không đụng tới** (thuộc phần của thành viên khác): `admin/*`, `layouts/admin`, `layouts/player`, `layouts/app`, `navigation`, `welcome`. Các trang này không nạp theme-script nên vẫn hiển thị nền sáng như cũ.

## 4. Lưu ý bảo mật (chưa sửa)
`courses/show` in mô tả khóa học bằng `{!! $course->description !!}` (HTML từ CKEditor). Nếu giảng viên chèn mã độc thì có nguy cơ **stored XSS**. Cách khắc phục đề xuất: lọc HTML khi lưu bằng một thư viện như `mews/purifier`. Việc này cần thêm dependency nên chưa làm, chờ nhóm đồng ý.

## 5. Kiểm tra
- `php artisan test --compact`: toàn bộ test pass.
- `vendor/bin/pint --dirty`: đã format.
- `npm run build`: build CSS / JS thành công.
- Đã xem thực tế trên trình duyệt ở cả hai chế độ: trang chủ, chi tiết khóa học, đăng nhập, Studio (quản lý nội dung, sửa khóa học kèm CKEditor). Không có thanh cuộn ngang.

## 6. Chạy thử trên máy
Dự án dùng **MySQL**. Tạo một database trống (ví dụ `elearning_final`, charset `utf8mb4`), khai báo `DB_CONNECTION=mysql`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` trong `.env`, rồi chạy **từng lệnh một** (Windows PowerShell 5.1 không hỗ trợ `&&`):

```powershell
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

> Nếu migrate báo `Table '...' already exists` thì database đang chứa bảng của phiên bản cũ: dùng database mới, hoặc `php artisan migrate:fresh --seed` (xóa toàn bộ dữ liệu cũ).

`.env` đã có trong `.gitignore`, không bị commit.
