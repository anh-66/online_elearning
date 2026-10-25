# Online E-Learning

Website học trực tuyến: giảng viên đăng khóa học, học viên đăng ký và học, quản trị viên kiểm duyệt nội dung. Đây là bài tập nhóm cuối kỳ môn **Lập trình Web** (PHP + MySQL).

## Mục lục

- [Tính năng](#tính-năng)
- [Công nghệ](#công-nghệ)
- [Yêu cầu môi trường](#yêu-cầu-môi-trường)
- [Cài đặt](#cài-đặt)
- [Tài khoản mặc định](#tài-khoản-mặc-định)
- [Cấu trúc thư mục](#cấu-trúc-thư-mục)
- [Phân quyền](#phân-quyền)
- [Quy trình làm việc nhóm](#quy-trình-làm-việc-nhóm)
- [Các lệnh hay dùng](#các-lệnh-hay-dùng)
- [Xử lý lỗi thường gặp](#xử-lý-lỗi-thường-gặp)
- [Thành viên nhóm](#thành-viên-nhóm)

## Tính năng

Trạng thái: ✅ đã xong, 🚧 đang làm, ⬜ chưa làm. Cập nhật bảng này khi hoàn thành module.

| Nhóm | Tính năng | Trạng thái |
|---|---|---|
| Xác thực | Đăng ký, đăng nhập, đăng xuất, quên/đổi mật khẩu, hồ sơ cá nhân | ✅ (Breeze) |
| Phân quyền | Middleware theo vai trò, redirect theo vai trò, Policy quyền sở hữu | ⬜ |
| Admin | Dashboard thống kê (Chart.js) | ⬜ |
| Admin | CRUD người dùng (khóa/mở khóa, đổi vai trò) | ⬜ |
| Admin | CRUD danh mục | ⬜ |
| Admin | Duyệt/từ chối/ẩn khóa học | ⬜ |
| Admin | Quản lý đánh giá, bình luận, đăng ký học | ⬜ |
| Giảng viên | CRUD khóa học (editor, upload nhiều ảnh) | ⬜ |
| Giảng viên | CRUD chương, bài học | ⬜ |
| Giảng viên | CRUD quiz, câu hỏi, đáp án | ⬜ |
| Giảng viên | Danh sách học viên, tiến độ, thống kê | ⬜ |
| Học viên | Trang chủ, danh sách khóa học (tìm kiếm, lọc, phân trang) | ⬜ |
| Học viên | Chi tiết khóa học, đăng ký học | ⬜ |
| Học viên | Khóa học của tôi, trang học bài, theo dõi tiến độ | ⬜ |
| Học viên | Làm quiz, chấm điểm tự động | ⬜ |
| Học viên | Đánh giá sao, bình luận | ⬜ |
| Mở rộng | Wishlist, chứng chỉ PDF, xuất Excel | ⬜ |

## Công nghệ

| Thành phần | Công nghệ |
|---|---|
| Backend | PHP 8.3+, Laravel 13, Eloquent ORM |
| Cơ sở dữ liệu | MySQL |
| Xác thực | Laravel Breeze (Blade) |
| Frontend | Blade, Tailwind CSS 3, Alpine.js, Vite |
| Editor | CKEditor 5 |
| Biểu đồ | Chart.js |
| Xử lý ảnh | Intervention Image |
| Xuất file | barryvdh/laravel-dompdf (PDF), maatwebsite/excel (Excel) |
| Giao diện chọn nhiều | Tom Select |
| Test | PHPUnit |

## Yêu cầu môi trường

Cả nhóm dùng **cùng phiên bản** để tránh lỗi "máy tôi chạy được".

- PHP **8.3 trở lên**, kèm các extension: `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd`, `zip`
- [Composer](https://getcomposer.org/) 2.x
- [Node.js](https://nodejs.org/) bản LTS mới (khuyến nghị 22 trở lên) và npm
- MySQL 8 hoặc MariaDB (có thể dùng XAMPP, Laragon, hoặc Docker)
- Git

Kiểm tra nhanh:

```bash
php -v
composer -V
node -v
mysql --version
```

## Cài đặt

**1. Clone dự án**

```bash
git clone https://github.com/anh-66/online_elearning.git
cd online_elearning
```

**2. Cài thư viện PHP và JavaScript**

```bash
composer install
npm install
```

**3. Tạo file môi trường**

```bash
# Windows (PowerShell/CMD):  copy .env.example .env
cp .env.example .env
php artisan key:generate
```

**4. Tạo database**

Tạo một database rỗng tên `online_elearning` (bảng mã `utf8mb4_unicode_ci`) bằng phpMyAdmin hoặc lệnh:

```sql
CREATE DATABASE online_elearning CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Mở `.env` và chỉnh thông tin kết nối cho đúng với máy của bạn:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=online_elearning
DB_USERNAME=root
DB_PASSWORD=
```

> Không commit file `.env`. File này đã nằm trong `.gitignore`.

**5. Tạo bảng và dữ liệu mẫu**

```bash
php artisan migrate --seed
```

**6. Liên kết thư mục lưu file upload**

```bash
php artisan storage:link
```

**7. Chạy dự án** (mở hai terminal)

```bash
# Terminal 1: server Laravel
php artisan serve

# Terminal 2: Vite (biên dịch CSS/JS, tự reload khi sửa code)
npm run dev
```

Truy cập: <http://localhost:8000>

> Nếu chỉ muốn chạy thử mà không sửa giao diện, dùng `npm run build` một lần thay cho `npm run dev`.

**Cài nhanh bằng một lệnh** (tùy chọn): `composer setup` sẽ cài thư viện, tạo `.env`, sinh key, chạy migrate và build frontend. Lệnh này **chưa chạy seeder**, bạn cần chạy thêm `php artisan db:seed` sau đó.

## Tài khoản mặc định

| Vai trò | Email | Mật khẩu |
|---|---|---|
| Admin | `admin@gmail.com` | `12345678` |

Tài khoản giảng viên và học viên mẫu sẽ được bổ sung trong seeder. **Đổi mật khẩu admin trước khi deploy.**

## Cấu trúc thư mục

```
online_elearning/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Controller (Auth/, ProfileController, ...)
│   │   └── Requests/           # Form Request: validate dữ liệu
│   ├── Models/                 # Eloquent models
│   ├── Providers/
│   └── View/Components/        # Layout component (AppLayout, GuestLayout)
├── bootstrap/app.php           # Khai báo middleware, routing
├── config/                     # Cấu hình ứng dụng
├── database/
│   ├── factories/              # Sinh dữ liệu giả
│   ├── migrations/             # Cấu trúc bảng
│   └── seeders/                # Dữ liệu mẫu
├── public/                     # Thư mục gốc web server trỏ vào
├── resources/
│   ├── css/app.css
│   ├── js/app.js
│   └── views/                  # Giao diện Blade
│       ├── auth/               # Đăng nhập, đăng ký, ...
│       ├── components/         # Blade component dùng lại
│       ├── layouts/
│       └── profile/
├── routes/
│   ├── web.php                 # Route chính
│   └── auth.php                # Route xác thực (Breeze)
├── storage/                    # Log, cache, file upload
└── tests/                      # Test (Feature, Unit)
```

Quy ước đặt tên khi thêm module mới (ví dụ quản lý khóa học của giảng viên):

| Loại | Vị trí | Ví dụ |
|---|---|---|
| Controller admin | `app/Http/Controllers/Admin/` | `Admin\CategoryController` |
| Controller giảng viên | `app/Http/Controllers/Instructor/` | `Instructor\CourseController` |
| Controller học viên/public | `app/Http/Controllers/` | `CourseController` |
| View admin | `resources/views/admin/` | `admin/categories/index.blade.php` |
| View giảng viên | `resources/views/instructor/` | `instructor/courses/create.blade.php` |
| Route | Gom theo nhóm có prefix `admin`, `instructor` | `/admin/categories` |

## Phân quyền

Vai trò lưu ở cột `role` của bảng `users`:

| Vai trò | Giá trị `role` | Quyền |
|---|---|---|
| Khách | (chưa đăng nhập) | Xem trang chủ, danh sách và chi tiết khóa học |
| Học viên | `student` | Đăng ký học, học bài, làm quiz, đánh giá, bình luận |
| Giảng viên | `instructor` | Quản lý khóa học, bài học, quiz **của chính mình** |
| Quản trị | `admin` | Toàn quyền quản lý hệ thống |

Nguyên tắc: **mọi trang và mọi hành động sửa/xóa đều phải kiểm tra quyền**, kể cả quyền sở hữu (giảng viên chỉ sửa được khóa học của mình). Khi cho người dùng tự đăng ký, chỉ chấp nhận `student` hoặc `instructor`, tuyệt đối không nhận `admin` từ form.

Vòng đời trạng thái của khóa học: `draft` → `pending` → `published` (hoặc `rejected` → `draft`, hoặc `hidden`).

## Quy trình làm việc nhóm

**Nhánh**

- `main`: luôn chạy được, **không push trực tiếp**.
- Mỗi người làm trên nhánh riêng theo tính năng: `feature/<tên-tính-năng>`, ví dụ `feature/admin-category-crud`, `fix/login-redirect`.

```bash
git checkout main
git pull origin main
git checkout -b feature/admin-category-crud
# ... làm việc, commit ...
git push -u origin feature/admin-category-crud
```

Sau đó mở **Pull Request** vào `main` trên GitHub, ít nhất một bạn khác xem qua rồi mới merge.

**Commit message** theo dạng `loại: mô tả ngắn`:

| Tiền tố | Dùng khi |
|---|---|
| `feat:` | Thêm tính năng mới |
| `fix:` | Sửa lỗi |
| `refactor:` | Chỉnh code, không đổi hành vi |
| `style:` | Giao diện, định dạng |
| `docs:` | Tài liệu |
| `chore:` | Cấu hình, thư viện |

Ví dụ: `feat: thêm CRUD danh mục cho admin`.

**Lưu ý tránh xung đột**

- Trước khi làm, luôn `git pull origin main` để lấy bản mới nhất.
- **Migration:** không sửa migration đã được merge vào `main`. Cần đổi cấu trúc bảng thì tạo migration mới bằng `php artisan make:migration`.
- Sau khi pull về có migration mới, chạy `php artisan migrate`. Nếu bị lỗi lệch dữ liệu, chạy `php artisan migrate:fresh --seed` (sẽ xóa sạch dữ liệu local).
- Không commit `.env`, `vendor/`, `node_modules/`, file upload.

## Các lệnh hay dùng

```bash
php artisan migrate                 # chạy migration mới
php artisan migrate:fresh --seed    # xóa hết bảng, tạo lại và nạp dữ liệu mẫu
php artisan db:seed                 # chỉ nạp dữ liệu mẫu
php artisan route:list              # xem danh sách route
php artisan make:model Course -mfc  # tạo model + migration + factory + controller
php artisan make:controller Admin/CategoryController --resource
php artisan make:request StoreCourseRequest
php artisan make:policy CoursePolicy --model=Course
php artisan optimize:clear          # xóa cache cấu hình, route, view
php artisan test                    # chạy test
./vendor/bin/pint                   # tự động định dạng code theo chuẩn
```

## Xử lý lỗi thường gặp

| Lỗi | Cách xử lý |
|---|---|
| `No application encryption key has been specified` | Chạy `php artisan key:generate` |
| `SQLSTATE[HY000] [1049] Unknown database` | Chưa tạo database `online_elearning` (xem bước 4) |
| `SQLSTATE[HY000] [2002] Connection refused` | MySQL chưa bật, hoặc sai `DB_HOST`/`DB_PORT` trong `.env` |
| `Vite manifest not found` | Chạy `npm run dev` hoặc `npm run build` |
| Ảnh upload không hiển thị | Chạy `php artisan storage:link` |
| Sửa `.env` hoặc config mà không có tác dụng | Chạy `php artisan optimize:clear` |
| `Your lock file does not contain a compatible set of packages` | Phiên bản PHP chưa đủ (cần 8.3+) hoặc thiếu extension |
| Sau khi pull, giao diện lỗi hoặc thiếu class | Chạy lại `composer install` và `npm install` |

## Thành viên nhóm

| STT | Họ tên | MSSV | Phụ trách |
|---|---|---|---|
| 1 | | | |
| 2 | | | |
| 3 | | | |
| 4 | | | |
| 5 | | | |

---

Bài tập nhóm môn Lập trình Web. Xây dựng trên [Laravel](https://laravel.com) (giấy phép MIT).