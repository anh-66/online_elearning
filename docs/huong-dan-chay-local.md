# Hướng dẫn chạy project ở máy (local)

Làm theo thứ tự từ trên xuống. Không cần API key nào vẫn chạy được đầy đủ
chức năng chính. Các key chỉ bật thêm đăng nhập Google, chat AI, lưu ảnh lên
Cloudinary và gửi email thật (xem [api-keys/README.md](api-keys/README.md)).

## 1. Cài phần mềm cần có

| Phần mềm | Phiên bản | Ghi chú |
|---|---|---|
| PHP | 8.3 trở lên | Cần extension `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd`, `zip`, `curl` |
| Composer | 2.x | <https://getcomposer.org/> |
| Node.js + npm | Bản LTS | <https://nodejs.org/> |
| MySQL | 8 (hoặc MariaDB) | XAMPP hoặc Laragon trên Windows là đủ |
| Git | bất kỳ | |

Trên Windows dễ nhất là cài **Laragon** (có sẵn PHP, MySQL, Composer) hoặc
**XAMPP**. Với XAMPP, mở `php.ini` và bỏ dấu `;` trước các dòng
`extension=gd`, `extension=zip`, `extension=fileinfo` nếu đang bị tắt.

Kiểm tra:

```bash
php -v
composer -V
node -v
npm -v
```

## 2. Lấy mã nguồn

```bash
git clone https://github.com/anh-66/online_elearning.git
cd online_elearning
git checkout merge-main-render
```

Nếu nhánh đã được merge vào `main` thì dùng `git checkout main`.

## 3. Cài thư viện

```bash
composer install
npm install
```

## 4. Tạo file `.env`

Windows PowerShell:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

Git Bash / macOS / Linux:

```bash
cp .env.example .env
php artisan key:generate
```

Các giá trị mặc định trong `.env.example` đã dùng được cho máy local:

```env
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=online_elearning
DB_USERNAME=root
DB_PASSWORD=
MAIL_MAILER=log
EMAIL_OTP_ENABLED=true
```

Nếu MySQL của bạn có mật khẩu root hoặc chạy cổng khác thì sửa `DB_PASSWORD`,
`DB_PORT` cho đúng.

## 5. Tạo database

Mở phpMyAdmin (XAMPP: <http://localhost/phpmyadmin>) hoặc HeidiSQL (Laragon),
chạy:

```sql
CREATE DATABASE online_elearning CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 6. Tạo bảng, dữ liệu mẫu và link thư mục ảnh

```bash
php artisan migrate --seed
php artisan storage:link
```

`--seed` tạo sẵn tài khoản, danh mục, khóa học mẫu. Muốn seed lại từ đầu (xóa
hết dữ liệu cũ):

```bash
php artisan migrate:fresh --seed
```

Lệnh an toàn hơn, chỉ seed khi database chưa có người dùng nào:

```bash
php artisan app:seed-if-empty
```

## 7. Chạy web

Cách 1, một lệnh chạy tất cả (web server, queue, Vite):

```bash
composer run dev
```

Cách 2, mở hai terminal:

```bash
# Terminal 1
php artisan serve
```

```bash
# Terminal 2
npm run dev
```

Không muốn chạy `npm run dev` thì build CSS/JS một lần là đủ:

```bash
npm run build
```

Mở <http://localhost:8000>. Nên luôn mở bằng `localhost` cho khớp với
`APP_URL` (đăng nhập Google cần điều này).

## 8. Tài khoản có sẵn

Mật khẩu tất cả là `Password123`.

| Vai trò | Email |
|---|---|
| Admin | `admin@gmail.com` |
| Giảng viên | `instructor1@gmail.com`, `instructor2@gmail.com`, `instructor@gmail.com` |
| Học viên | `student1@gmail.com`, `student2@gmail.com`, `student3@gmail.com`, `student4@gmail.com`, `student@gmail.com` |

Trang admin: <http://localhost:8000/admin/dashboard>.

## 9. Mã OTP khi đăng ký và quên mật khẩu

Ở máy local, mail **không gửi thật** (`MAIL_MAILER=log`). Khi đăng ký tài khoản
mới hoặc quên mật khẩu, mã 6 số nằm ở một trong hai chỗ:

- File `storage/logs/laravel.log`, kéo xuống cuối, tìm mail mới nhất.
- Đăng nhập admin, vào **Email đã gửi** (<http://localhost:8000/admin/email-logs>).

Mã hết hạn sau 10 phút, nhập sai tối đa 5 lần.

Không muốn bước nhập mã khi đăng ký, sửa trong `.env`:

```env
EMAIL_OTP_ENABLED=false
```

```bash
php artisan config:clear
```

Muốn mail tới hộp thư thật: xem [api-keys/brevo.md](api-keys/brevo.md).

## 10. Bật thêm chức năng cần API key (tùy chọn)

| Chức năng | Hướng dẫn |
|---|---|
| Đăng nhập bằng Google | [api-keys/google-oauth.md](api-keys/google-oauth.md) |
| Chat AI tư vấn khóa học | [api-keys/gemini.md](api-keys/gemini.md) |
| Lưu ảnh lên Cloudinary, trang Admin > Ảnh Cloudinary | [api-keys/cloudinary.md](api-keys/cloudinary.md) |
| Gửi email thật | [api-keys/brevo.md](api-keys/brevo.md) |

Sau khi sửa `.env` luôn chạy `php artisan config:clear`.

## 11. Sau khi `git pull` code mới

```bash
composer install
npm install
php artisan migrate
npm run build
php artisan optimize:clear
```

Nếu có thêm biến mới trong `.env.example`, copy dòng đó sang `.env` của bạn.

## 12. Chạy test

```bash
php artisan test
```

Test dùng SQLite trong bộ nhớ, không đụng database MySQL của bạn. Cần chạy
`npm run build` ít nhất một lần trước đó, nếu không nhiều test sẽ báo lỗi
`Vite manifest not found`.

## 13. Lỗi hay gặp

| Lỗi | Cách sửa |
|---|---|
| `No application encryption key has been specified` | `php artisan key:generate` |
| `Vite manifest not found` | Chạy `npm run dev` (để mở) hoặc `npm run build` |
| `SQLSTATE[HY000] [1049] Unknown database` | Chưa tạo database, làm bước 5 |
| `SQLSTATE[HY000] [2002]` / `Connection refused` | MySQL chưa bật. Bật MySQL trong XAMPP/Laragon |
| `SQLSTATE[HY000] [1045] Access denied` | Sai `DB_USERNAME` / `DB_PASSWORD` trong `.env` |
| `Table 'sessions' doesn't exist` / `Table 'cache' doesn't exist` | Chưa migrate, chạy `php artisan migrate` |
| Ảnh upload không hiện (lỗi 404) | `php artisan storage:link` |
| `could not find driver` | Bật `extension=pdo_mysql` trong `php.ini` |
| Sửa `.env` mà không có tác dụng | `php artisan config:clear` rồi tắt bật lại `php artisan serve` |
| `composer install` đứng rất lâu | Thử lại với mạng khác, hoặc `composer install --prefer-dist`. Báo lỗi thiếu extension thì bật extension đó trong `php.ini` |
| Giao diện vỡ, không có CSS | Chưa chạy `npm run dev` / `npm run build`. Nếu đã tắt `npm run dev` thì xóa file `public/hot` rồi chạy `npm run build` |
