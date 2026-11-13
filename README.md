# Online E-Learning

Nền tảng học trực tuyến xây dựng bằng Laravel. Hệ thống hỗ trợ học viên tìm
kiếm và học khóa học, giảng viên xây dựng nội dung, và quản trị viên kiểm duyệt
vận hành toàn bộ nền tảng.

## Tính năng

Các thành viên đã hoàn thành các module chính của dự án:

### Người dùng và xác thực

- Đăng ký, đăng nhập, đăng xuất, quên và đặt lại mật khẩu.
- Xác minh email, đổi mật khẩu, cập nhật hồ sơ và avatar.
- Đăng nhập bằng Google OAuth.
- Xuất dữ liệu cá nhân và xóa tài khoản.
- Phân quyền theo ba vai trò: `student`, `instructor`, `admin`.

### Học viên và khu vực công khai

- Trang chủ, banner, danh mục và danh sách khóa học.
- Tìm kiếm, lọc, sắp xếp và phân trang khóa học.
- Xem chi tiết khóa học và hồ sơ giảng viên.
- Đăng ký khóa học và thanh toán qua quy trình checkout.
- Danh sách khóa học của tôi, trình phát bài học và theo dõi tiến độ.
- Quiz trắc nghiệm, chấm điểm, lịch sử làm bài và xem kết quả.
- Đánh giá sao, phản hồi của giảng viên và bình luận hỏi đáp.
- Wishlist, thông báo và gửi báo cáo sự cố.

### Studio giảng viên

- Tạo, sửa, xóa khóa học và quản lý thư viện ảnh.
- Quản lý chương, bài học, video và file đính kèm.
- Tạo quiz, câu hỏi và đáp án.
- Gửi khóa học để duyệt, rút yêu cầu duyệt và theo dõi trạng thái.
- Xem danh sách học viên, tiến độ và thống kê khóa học.
- Theo dõi thu nhập và gửi yêu cầu rút tiền.

### Quản trị viên

- Dashboard thống kê.
- Quản lý người dùng, trạng thái tài khoản và vai trò.
- Tạo tài khoản giảng viên.
- Quản lý danh mục và banner.
- Duyệt, từ chối, ẩn và xóa khóa học.
- Quản lý lượt ghi danh, đánh giá, bình luận và phản hồi sự cố.
- Quản lý thông báo hệ thống.
- Duyệt hoặc từ chối yêu cầu chi trả cho giảng viên.

## Công nghệ

| Thành phần | Công nghệ |
|---|---|
| Backend | PHP 8.3+, Laravel 13, Eloquent ORM |
| Cơ sở dữ liệu | MySQL |
| Xác thực | Laravel Breeze, Laravel Socialite |
| Frontend | Blade, React, Tailwind CSS 3, Alpine.js, Vite |
| Soạn thảo nội dung | CKEditor 5 |
| Biểu đồ | Chart.js |
| Xử lý ảnh | Intervention Image |
| Xuất file | Dompdf, Laravel Excel |
| Kiểm thử | PHPUnit |

## Yêu cầu môi trường

- PHP 8.3 trở lên với các extension: `mbstring`, `openssl`, `pdo_mysql`,
  `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd`, `zip`.
- Composer 2.x.
- Node.js bản LTS và npm.
- MySQL 8 hoặc MariaDB.
- Git.

Kiểm tra phiên bản:

```bash
php -v
composer -V
node -v
npm -v
```

## Cài đặt

### 1. Tải mã nguồn và cài dependency

```bash
git clone https://github.com/anh-66/online_elearning.git
cd online_elearning
composer install
npm install
```

### 2. Cấu hình môi trường

Windows PowerShell:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Linux/macOS:

```bash
cp .env.example .env
php artisan key:generate
```

Tạo database rỗng, sau đó cấu hình các biến sau trong `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=online_elearning
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Tạo bảng và dữ liệu mẫu

```bash
php artisan migrate --seed
php artisan storage:link
```

### 4. Cấu hình Google OAuth (tùy chọn)

Hướng dẫn từng bước cho mọi API key (Google, Gemini, Cloudinary, Brevo) nằm ở
[docs/api-keys](docs/api-keys/README.md), cách chạy project từ đầu ở
[docs/huong-dan-chay-local.md](docs/huong-dan-chay-local.md).

Tạo OAuth 2.0 Client ID loại Web application trong
[Google Cloud Console](https://console.cloud.google.com/), thêm callback URL
`http://localhost:8000/auth/google/callback`, rồi khai báo:

```env
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

Sau khi thay đổi biến môi trường:

```bash
php artisan optimize:clear
```

Không commit `.env` hoặc các secret vào Git.

### 5. Chạy ứng dụng

Mở hai terminal:

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

Truy cập `http://localhost:8000`. Khi không cần Vite ở chế độ phát triển,
có thể build frontend bằng:

```bash
npm run build
```

Có thể dùng `composer setup` để cài dependency, tạo `.env`, sinh application
key, migrate và build frontend. Seeder cần chạy thêm bằng
`php artisan db:seed` nếu chưa được chạy.

## Tài khoản dữ liệu mẫu

Các tài khoản dưới đây được tạo bởi `php artisan migrate --seed`:

| Vai trò | Email | Mật khẩu |
|---|---|---|
| Admin | `admin@gmail.com` | `Password123` |
| Giảng viên | `instructor1@gmail.com` | `Password123` |
| Giảng viên | `instructor2@gmail.com` | `Password123` |
| Học viên | `student1@gmail.com` | `Password123` |
| Học viên | `student2@gmail.com` | `Password123` |
| Học viên | `student3@gmail.com` | `Password123` |

Tài khoản alias `instructor@gmail.com` và `student@gmail.com` cũng được seed
để tương thích với dữ liệu hoặc test cũ. Hãy đổi mật khẩu trước khi triển khai
thực tế.

## Phân quyền và vòng đời khóa học

| Vai trò | Quyền chính |
|---|---|
| Khách | Xem trang chủ, danh mục, khóa học và hồ sơ giảng viên |
| Học viên (`student`) | Ghi danh, học bài, làm quiz, wishlist, đánh giá và bình luận |
| Giảng viên (`instructor`) | Quản lý nội dung khóa học của mình, học viên và thu nhập |
| Quản trị viên (`admin`) | Quản trị người dùng, nội dung, vận hành, kiểm duyệt và chi trả |

Khóa học đi qua các trạng thái:

```text
draft -> pending -> published
              \-> rejected -> draft
published -> hidden
```

Các route của giảng viên dùng prefix `/instructor` và middleware
`role:instructor,admin`. Các route quản trị dùng prefix `/admin` và middleware
`role:admin`. Policy kiểm tra quyền sở hữu khóa học và các tài nguyên liên quan.

## Cấu trúc thư mục

```text
app/
├── Http/Controllers/
│   ├── Admin/              # Chức năng quản trị
│   ├── Auth/               # Xác thực
│   └── Instructor/         # Studio giảng viên
├── Models/                 # Eloquent models
├── Policies/               # Phân quyền tài nguyên
└── View/Components/        # Blade components
bootstrap/app.php           # Đăng ký route và middleware
config/                     # Cấu hình ứng dụng
database/
├── factories/              # Factory cho test
├── migrations/             # Cấu trúc cơ sở dữ liệu
└── seeders/                # Dữ liệu mẫu
resources/
├── css/                    # Tailwind và CSS ứng dụng
├── js/                     # JavaScript, React và CKEditor
└── views/                  # Blade views
routes/
├── web.php                 # Route công khai, học viên và hồ sơ
├── auth.php                # Route xác thực
├── instructor.php          # Route Studio giảng viên
└── admin.php               # Route quản trị
storage/                    # Log, cache và file upload
tests/                      # Feature tests và unit tests
```

## Lệnh thường dùng

```bash
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed
php artisan route:list
php artisan optimize:clear
php artisan test
vendor/bin/pint
npm run build
```

## Xử lý lỗi thường gặp

| Lỗi | Cách xử lý |
|---|---|
| `No application encryption key has been specified` | Chạy `php artisan key:generate` |
| `Unknown database` | Tạo database và kiểm tra `DB_*` trong `.env` |
| `Connection refused` | Bật MySQL hoặc kiểm tra `DB_HOST` và `DB_PORT` |
| `Vite manifest not found` | Chạy `npm run dev` hoặc `npm run build` |
| Ảnh upload không hiển thị | Chạy `php artisan storage:link` |
| Config hoặc `.env` chưa được nhận | Chạy `php artisan optimize:clear` |
| `redirect_uri_mismatch` | Đồng bộ `APP_URL`, `GOOGLE_REDIRECT_URI` và callback trên Google Cloud |
| Thiếu package hoặc class | Chạy lại `composer install` và `npm install` |

## Quy ước đóng góp

- Tạo branch riêng theo tính năng, không push trực tiếp vào `main`.
- Dùng Pull Request để review trước khi merge.
- Đặt commit message theo dạng `feat:`, `fix:`, `refactor:`, `style:`,
  `docs:` hoặc `chore:`.
- Không commit `.env`, `vendor/`, `node_modules/` và file upload.
- Không sửa migration đã được merge; tạo migration mới khi thay đổi schema.

---

Dự án nhóm môn Lập trình Web, xây dựng trên
[Laravel](https://laravel.com) và phát hành theo giấy phép MIT.
