# Công nghệ sử dụng: deploy, lưu file, chat AI, trang ảnh admin và xác thực email

> Tài liệu này liệt kê các công nghệ dùng trong những phần làm thêm trên nhánh `deploy-render`:
> đưa web lên Render, lưu file upload trên Cloudinary, đăng nhập Google, chat AI tư vấn khóa học
> trang **Ảnh Cloudinary** trong admin và xác thực email bằng mã OTP. Tất cả dịch vụ bên ngoài đều dùng gói **miễn phí**,
> và không cài thêm package Composer nào mới.

## 1. Tổng quan

| Phần | Công nghệ chính | Chi phí |
|---|---|---|
| Đưa web lên Internet | Docker, Apache, Render (Blueprint `render.yaml`) | Render gói Free |
| Database online | MySQL trên cloud (Aiven hoặc TiDB Cloud), kết nối SSL | Gói Free |
| Lưu file upload | Cloudinary (Upload API, CDN), Flysystem adapter tự viết | Cloudinary gói Free |
| Đăng nhập Google | Laravel Socialite, Google OAuth 2.0 | Miễn phí |
| Chat AI tư vấn khóa học | Google Gemini API, Laravel HTTP Client, Rate Limiter | Gemini gói Free |
| Trang Ảnh Cloudinary (admin) | Cloudinary Admin API, Laravel Cache, Alpine.js | Miễn phí |
| Xác thực email, quên mật khẩu | Mã OTP 6 số gửi qua Brevo API, Laravel Mail (driver tự viết), Rate Limiter | Brevo gói Free |
| Kiểm thử | PHPUnit, `Http::fake()`, Laravel Pint | Miễn phí |

## 2. Nền tảng có sẵn của dự án

Các phần dưới đây xây trên nền tảng mà nhóm đã chọn từ đầu:

- **Laravel 13** (PHP 8.3 trở lên), giao diện **Blade**.
- **Tailwind CSS 3**, **Alpine.js 3**, build bằng **Vite**.
- **MySQL** làm database chính.
- **Laravel Breeze** (đăng nhập, đăng ký), **Laravel Socialite** (đăng nhập Google).
- **Chart.js** (biểu đồ), **CKEditor 5** (soạn nội dung bài học).

## 3. Đưa web lên Render bằng Docker

Render chạy web dưới dạng container Docker. Image được build theo kiểu **multi-stage** để image cuối gọn nhẹ:

| Stage | Image gốc | Việc làm |
|---|---|---|
| `base` | `php:8.4-apache-bookworm` | Cài extension PHP: `pdo_mysql`, `gd`, `zip`, `intl`, `exif`, `opcache` (qua `mlocati/php-extension-installer`) và Composer 2 |
| `vendor` | `base` | `composer install --no-dev`, chỉ cài package chạy production |
| `assets` | `node:22-bookworm-slim` | `npm ci` rồi `npm run build` (Vite build CSS/JS) |
| `app` | `base` | Ghép code, `vendor/`, `public/build/` thành image cuối |

Các thành phần khác:

- **Apache**: `DocumentRoot` trỏ vào `public/`, bật `mod_rewrite` cho route Laravel, nghe ở cổng `$PORT` (Render mặc định 10000).
- **PHP production** (`docker/php/app.ini`): bật OPcache, cho upload tối đa 25 MB.
- **Script khởi động** `docker/entrypoint.sh` chạy mỗi lần container bật:
  1. Báo lỗi rõ ràng nếu thiếu `APP_KEY`.
  2. Tự đặt `APP_URL` từ `RENDER_EXTERNAL_URL` và `GOOGLE_REDIRECT_URI` từ `APP_URL`.
  3. Chép file CA của MySQL ra chỗ PHP đọc được.
  4. Chạy `php artisan migrate`, thử lại tối đa 5 lần vì database cloud có thể chậm nhận kết nối.
  5. Chạy seeder nếu `RUN_SEEDERS=true`, nhưng chỉ khi database còn trống (lệnh `php artisan app:seed-if-empty`). Nhờ vậy ảnh banner, avatar, ảnh khóa học admin đã sửa không bị seeder ghi đè mỗi lần Render khởi động lại. `RUN_SEEDERS=force` thì seed lại mỗi lần như cũ.
  6. Chạy `php artisan optimize` để cache config, route, view.
- **Render Blueprint** (`render.yaml`): khai báo service bằng code (Infrastructure as Code). Gói Free, vùng Singapore, health check ở `/up`, tự deploy khi push. Các biến bí mật để `sync: false` nên không nằm trong Git.

File liên quan: `Dockerfile`, `.dockerignore`, `render.yaml`, `docker/entrypoint.sh`, `docker/apache/vhost.conf`, `docker/php/app.ini`.

## 4. Database MySQL trên cloud

- Render không có MySQL managed, nên database đặt ở dịch vụ MySQL cloud bên ngoài: **Aiven for MySQL** hoặc **TiDB Cloud Starter** (tương thích MySQL, gói free 5 GiB).
- Kết nối bằng **PDO MySQL qua SSL/TLS**, cấu hình bằng biến `MYSQL_ATTR_SSL_CA`:
  - Aiven: tải `ca.pem`, thêm vào Render dạng Secret File, điền `/etc/secrets/ca.pem`.
  - TiDB Cloud: dùng bộ CA của hệ thống, điền `/etc/ssl/certs/ca-certificates.crt`.
- Session và cache lưu trong database (`SESSION_DRIVER=database`, `CACHE_STORE=database`), vì container trên Render không giữ file khi khởi động lại.

> Lưu ý: Aiven đã ngừng gói MySQL miễn phí. Các dịch vụ free cũ bị tắt và xóa dữ liệu ngày 21/12/2026.
> Nếu đang dùng Aiven gói Free, cần chuyển sang TiDB Cloud (hoặc dịch vụ khác) trước ngày đó.

## 5. Lưu file upload trên Cloudinary

**Vấn đề:** Render gói Free không có ổ đĩa lưu lâu dài. Avatar, ảnh khóa học, tài liệu upload vào `storage/app/public` sẽ mất mỗi lần deploy hoặc restart.

**Giải pháp:** lưu file lên **Cloudinary**, dịch vụ lưu trữ và CDN cho ảnh.

- **Flysystem adapter tự viết** (`app/Filesystem/CloudinaryAdapter.php`): Laravel dùng thư viện League Flysystem 3 cho `Storage`. Adapter cài interface `FilesystemAdapter` và được đăng ký bằng `Storage::extend('cloudinary', ...)` trong `AppServiceProvider`. Không dùng SDK vì gói `cloudinary-labs/cloudinary-laravel` chưa hỗ trợ Laravel 13.
- **Cloudinary Upload API**: gửi file bằng Laravel HTTP Client, tham số được **ký bằng SHA-1** (sắp xếp tham số, nối với API secret) theo chuẩn của Cloudinary.
- Ảnh (`jpg`, `png`, `webp`...) upload dạng `image`. File khác (`pdf`, `docx`, `zip`...) upload dạng `raw`.
- Đường dẫn Laravel (vd. `courses/thumbnails/abc.jpg`) được giữ làm public ID. Vì vậy dữ liệu trong database không đổi, và URL ảnh (`https://res.cloudinary.com/...`) được tính ra mà không cần gọi API.
- **Tự chuyển đổi:** disk `public` chỉ dùng Cloudinary khi có biến `CLOUDINARY_URL`. Không có biến này (vd. chạy trên máy) thì vẫn lưu vào `storage/app/public` như bình thường.

File liên quan: `app/Filesystem/CloudinaryAdapter.php`, `config/filesystems.php`, `app/Providers/AppServiceProvider.php`.

## 6. Đăng nhập Google và ảnh đại diện

- **Laravel Socialite** với **Google OAuth 2.0** để đăng nhập bằng tài khoản Google.
- Lấy ảnh đại diện Google cho người dùng chưa có avatar riêng, và không ghi đè avatar người dùng tự upload.
- Ảnh từ Google được hiển thị với `referrerpolicy="no-referrer"` để Google không chặn ảnh.
- Form đổi avatar dùng **Alpine.js** để chặn bấm gửi hai lần.

File liên quan: `app/Http/Controllers/Auth/GoogleController.php`, các view có avatar, `resources/views/profile/partials/update-profile-information-form.blade.php`.

## 7. Chat AI tư vấn khóa học

Khung chat nổi trên các trang công khai, trả lời câu hỏi về khóa học dựa trên dữ liệu thật trong database.

- **Google Gemini API** (endpoint REST `generateContent`, bản `v1beta`), gọi bằng **Laravel HTTP Client**, không cài thêm package. Model chọn bằng biến `GEMINI_MODEL` (mặc định `gemini-3.5-flash-lite`).
- **Prompt (system instruction)**: gửi kèm danh sách tối đa 80 khóa học đã xuất bản (ưu tiên khóa đông học viên). Danh sách này được **cache** để không truy vấn database mỗi câu hỏi. Kèm tối đa 8 tin nhắn gần nhất để AI hiểu ngữ cảnh.
- **Giới hạn lượt hỏi** bằng **Laravel Rate Limiter**: 5 câu/phút và 30 câu/ngày cho mỗi người dùng (hoặc mỗi IP nếu chưa đăng nhập), để không vượt hạn mức miễn phí của Gemini.
- **Giao diện**: Blade partial + JavaScript thuần (`fetch` kèm CSRF token). Lịch sử chat lưu trong `sessionStorage`. Câu trả lời được hiển thị bằng `textContent` (không dùng `innerHTML`) để chống chèn mã độc (XSS).
- Không có `GEMINI_API_KEY` thì khung chat tự ẩn. Không bật thanh toán trên Google nên khi hết lượt, API chỉ báo lỗi 429 chứ không tính tiền.

File liên quan: `app/Services/GeminiClient.php`, `app/Services/CourseAdvisor.php`, `app/Services/GeminiException.php`, `app/Http/Controllers/CourseChatController.php`, `resources/views/partials/course-chat.blade.php`, route `POST /chat/courses`.

## 8. Trang Ảnh Cloudinary trong admin

Menu **Ảnh Cloudinary** (đường dẫn `/admin/cloudinary`) cho admin xem toàn bộ ảnh website đã lưu trên Cloudinary.

- **Cloudinary Admin API**: `GET resources/image/upload` lọc theo thư mục dự án (`prefix`), xác thực **HTTP Basic** bằng API key và API secret. Mỗi lần lấy tối đa 500 ảnh, lấy tiếp bằng `next_cursor`. Endpoint `usage` cho biết số credits đã dùng của gói Free.
- **Laravel Cache**: lưu danh sách 5 phút để chuyển trang, lọc không phải gọi lại API (gói Free giới hạn 500 lần gọi Admin API mỗi giờ). Nút **Làm mới** xóa cache.
- **Phân trang thủ công** bằng `LengthAwarePaginator` (24 ảnh mỗi trang), lọc theo thư mục và tìm theo tên file.
- **Cloudinary Transformations**: ảnh nhỏ được Cloudinary tạo sẵn qua URL (`c_fill,w_400,h_300/f_auto,q_auto`) nên trang tải nhanh và tốn ít băng thông.
- **Alpine.js** cho cửa sổ xem ảnh lớn, **Clipboard API** cho nút sao chép link (có cách dự phòng khi trình duyệt chặn).
- **Gắn / tháo ảnh**: mỗi ảnh cho biết đang dùng ở đâu (banner, ảnh bìa, thư viện ảnh khóa học, avatar, đính kèm) hoặc "Chưa dùng". Admin gắn ảnh có sẵn vào banner, ảnh bìa khóa học, avatar (chỉ hiện mục đang thiếu ảnh), hoặc **Tháo** ảnh khỏi một chỗ mà vẫn giữ file trên Cloudinary. Chỉ sửa đường dẫn trong database.
- **Thùng rác 7 ngày**: nút **Xóa** tháo ảnh khỏi mọi chỗ đang dùng rồi đưa vào bảng `cloudinary_trash` (ghi lại các chỗ cũ để **Khôi phục**). Hết 7 ngày, hoặc khi admin bấm **Xóa vĩnh viễn**, website gọi **Cloudinary Upload API `destroy`** (có ký SHA-1, `invalidate=true` để xóa cả bản cache trên CDN) để xóa ảnh thật trên Cloudinary.
- **Tự dọn không cần cron**: Render gói Free không có cron miễn phí, nên ảnh hết hạn được xóa khi admin mở trang Ảnh Cloudinary / Thùng rác và mỗi lần server khởi động (`php artisan cloudinary:purge-trash` trong `docker/entrypoint.sh`).
- **Chọn nhiều ảnh**: mỗi ảnh có ô tích (Alpine.js). Tích 1 ô là hiện thanh **Chọn tất cả** (trang này, rồi "chọn luôn cả các trang khác") để **xóa hàng loạt** vào thùng rác; trong Thùng rác thì **khôi phục** hoặc **xóa vĩnh viễn** hàng loạt. Danh sách đã chọn gửi lên dạng một chuỗi JSON để chọn hàng nghìn ảnh cũng không vướng giới hạn `max_input_vars` của PHP.
- **Giới hạn thời gian khi xóa vĩnh viễn**: Cloudinary xóa từng ảnh một, còn PHP trên Render dừng request sau 60 giây, nên mỗi lần bấm chỉ xóa trong khoảng 40 giây (tự dọn khi mở trang: 15 giây). Ảnh chưa kịp xóa vẫn nằm trong thùng rác, bấm lại để xóa tiếp.

File liên quan: `app/Services/CloudinaryImageLibrary.php`, `app/Services/CloudinaryImageAssigner.php`, `app/Services/CloudinaryTrashBin.php`, `app/Models/CloudinaryTrash.php`, `app/Console/Commands/PurgeCloudinaryTrashCommand.php`, `app/Http/Controllers/Admin/CloudinaryController.php`, `app/Http/Controllers/Admin/CloudinaryTrashController.php`, `resources/views/admin/cloudinary/` (`index`, `trash`, `partials/tabs`, `partials/selection-bar`), `routes/admin.php`, `resources/views/layouts/admin.blade.php`.

## 9. Xác thực email và quên mật khẩu bằng mã OTP

Tài khoản đăng ký bằng email phải nhập mã 6 số gửi về email thì mới dùng được web. Quên mật khẩu cũng nhận mã 6 số qua email thay cho link.

- **Brevo Transactional API** (`POST https://api.brevo.com/v3/smtp/email`, header `api-key`): Render gói Free chặn cổng SMTP 25/465/587 nên không gửi qua Gmail SMTP được; Brevo gửi qua HTTPS (cổng 443). Gói Free 300 mail/ngày, không cần thẻ.
- **Driver mail tự viết** `brevo` (`app/Mail/Transport/BrevoTransport.php`): một Symfony Mailer transport gọi API bằng **Laravel HTTP Client**, đăng ký bằng `Mail::extend()`. Không cài thêm package. Chỉ cần đổi `MAIL_MAILER` là chuyển giữa `brevo` (Render) và `log` (máy cá nhân).
- **Mã OTP** (`app/Services/EmailOtpService.php`, bảng `email_otps`): mã 6 số tạo bằng `random_int`, chỉ lưu bản băm (`Hash::make`). Hết hạn sau 10 phút, sai 5 lần phải xin mã mới, gửi lại cách nhau 60 giây, tối đa 5 mã/giờ cho mỗi tài khoản (**Laravel Rate Limiter**). Cấu hình trong `config/otp.php`.
- **Xác thực email**: `User` implements `MustVerifyEmail`, ghi đè `sendEmailVerificationNotification()` để gửi mã thay cho link. Middleware `EnsureEmailIsVerifiedWithOtp` (gắn vào nhóm `web`) đưa tài khoản chưa xác thực về trang nhập mã. Đăng nhập Google, giảng viên do admin tạo và mọi tài khoản có trước tính năng này được coi là đã xác thực. Đổi email trong hồ sơ không cần xác thực lại.
- **Quên mật khẩu** (3 bước: nhập email → nhập mã → tạo mật khẩu mới): trang tạo mật khẩu mới chỉ mở sau khi nhập đúng mã, và chỉ trong 10 phút (ghi nhận trong session, `NewPasswordController`). Email lấy từ session chứ không lấy từ form. Luôn trả lời giống nhau dù email có tồn tại hay không (nhập mã cho email lạ cũng báo "Mã không đúng"), để người lạ không dò được email đã đăng ký.
- **Giao diện**: ô nhập 6 số và nút "Gửi lại mã" có đếm ngược làm bằng **Alpine.js** (dán cả mã một lần được). Mail HTML viết bằng Blade, CSS inline.
- **Dự phòng**: Brevo lỗi thì mã vẫn dùng được và được ghi vào log (Render > Logs, tìm chữ `Brevo`). Đặt `EMAIL_OTP_ENABLED=false` để tắt hẳn bước nhập mã.
- **Trang admin "Email đã gửi"** (`/admin/email-logs`, `EmailLogController`, bảng `email_logs`): mỗi lần gửi mã được ghi lại là **Thành công** hay **Thất bại**, gửi qua Brevo hay chỉ ghi log, kèm lỗi Brevo trả về và gợi ý cách sửa (sai API key, chặn IP, chưa xác minh người gửi, hết lượt...). Lọc theo trạng thái, loại mail, tìm theo email. Không lưu mã OTP. Nhật ký giữ 90 ngày, tự dọn khi admin mở trang (không cần cron).

Cài Brevo: tạo tài khoản free ở brevo.com, vào Settings > Senders, Domains, IPs > **Add a sender** để xác nhận Gmail dùng làm địa chỉ gửi, vào Settings > SMTP & API > **API Keys & MCP** để tạo key, rồi vào Settings > Security > Authorized IPs bấm **Deactivate blocking** (IP của Render thay đổi). Gửi từ Gmail mà không có tên miền riêng thì mail có thể vào mục Spam.

File liên quan: `app/Mail/Transport/BrevoTransport.php`, `app/Mail/EmailOtpMail.php`, `app/Services/EmailOtpService.php`, `app/Services/EmailOtpResult.php`, `app/Models/EmailOtp.php`, `app/Models/EmailLog.php`, `app/Http/Controllers/Admin/EmailLogController.php`, `resources/views/admin/email-logs/`, `app/Http/Middleware/EnsureEmailIsVerifiedWithOtp.php`, `app/Http/Controllers/Auth/` (`EmailVerification*`, `VerifyEmailController`, `PasswordResetLinkController`, `PasswordResetCodeController`, `NewPasswordController`, `GoogleController`), `config/otp.php`, `config/mail.php`, `resources/views/auth/` (`verify-email`, `forgot-password`, `reset-password`, `new-password`), `resources/views/components/` (`otp-input`, `otp-resend-button`, `password-reset-steps`), `resources/views/emails/`, `routes/auth.php`.

## 10. Kiểm thử và chất lượng code

- **PHPUnit** (feature test của Laravel), database SQLite trong bộ nhớ khi test.
- **`Http::fake()`** giả lập Cloudinary, Gemini và Brevo, **`Mail::fake()`** bắt mail OTP, nên test chạy không cần mạng và không tốn lượt API.
- **Laravel Pint** để định dạng code theo chuẩn của Laravel.
- Các file test: `tests/Feature/CloudinaryStorageTest.php`, `tests/Feature/CourseChatTest.php`, `tests/Feature/Admin/CloudinaryImagesTest.php`, `tests/Feature/Admin/CloudinaryAssignImageTest.php`, `tests/Feature/Admin/CloudinaryTrashTest.php`, `tests/Feature/Admin/CloudinaryBulkDeleteTest.php`, `tests/Feature/Auth/GoogleAuthenticationTest.php`, `tests/Feature/Auth/EmailVerificationTest.php`, `tests/Feature/Auth/PasswordResetTest.php`, `tests/Feature/Mail/BrevoTransportTest.php`.

Chạy test:

```bash
php artisan test
```

## 11. Biến môi trường

| Biến | Dùng cho | Lấy ở đâu |
|---|---|---|
| `APP_KEY` | Mã hóa session, cookie | `php artisan key:generate --show` |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Kết nối MySQL | Trang quản lý của Aiven / TiDB Cloud |
| `MYSQL_ATTR_SSL_CA` | Đường dẫn file CA cho SSL | Xem mục 4 |
| `RUN_MIGRATIONS`, `RUN_SEEDERS` | Tự migrate, seed khi khởi động (seed chỉ chạy khi database trống) | Tự đặt `true` / `false` (`RUN_SEEDERS` có thêm `force`) |
| `CLOUDINARY_URL` | Lưu file lên Cloudinary | Cloudinary Console > API Keys > API environment variable |
| `CLOUDINARY_FOLDER` | Thư mục gốc trên Cloudinary (mặc định `online-elearning`) | Tự đặt |
| `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` | Đăng nhập Google | Google Cloud Console > Credentials |
| `GEMINI_API_KEY` | Chat AI | https://aistudio.google.com/apikey |
| `GEMINI_MODEL` | Tên model Gemini | Mặc định `gemini-3.5-flash-lite` |
| `MAIL_MAILER` | Cách gửi mail: `brevo` trên Render, `log` ở máy cá nhân | Tự đặt |
| `BREVO_API_KEY` | Gửi mail qua Brevo | Brevo > Settings > SMTP & API > API Keys & MCP |
| `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | Địa chỉ và tên người gửi | Địa chỉ đã xác nhận trong Brevo > Senders |
| `EMAIL_OTP_ENABLED` | Bật/tắt bước nhập mã OTP khi đăng ký (mặc định bật) | Tự đặt `true` / `false` |

## 12. Giữ chi phí bằng 0

- **Render Free**: web tự ngủ sau 15 phút không có người truy cập, lần mở đầu tiên sẽ chậm vài chục giây.
- **Cloudinary Free**: tính theo credits mỗi tháng. Trang Ảnh Cloudinary hiện số credits đã dùng để theo dõi.
- **Gemini Free**: không bật thanh toán, hết lượt thì chỉ báo lỗi, không bị trừ tiền.
- **Brevo Free**: 300 mail/ngày, không nhập thẻ. Web giới hạn mỗi tài khoản 5 mã/giờ nên khó chạm mức này.
- **Database**: dùng gói Free của TiDB Cloud (hoặc dịch vụ MySQL miễn phí khác), xem lưu ý về Aiven ở mục 4.
