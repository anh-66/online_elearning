# Công nghệ sử dụng: lưu file, chat AI, trang ảnh admin và xác thực email

> Tài liệu này liệt kê các công nghệ dùng trong những phần làm thêm (lấy từ nhánh `deploy-render`, chỉnh lại để chạy local):
> lưu file upload trên Cloudinary, đăng nhập Google, chat AI tư vấn khóa học,
> trang **Ảnh Cloudinary** trong admin và xác thực email bằng mã OTP. Tất cả dịch vụ bên ngoài đều dùng gói **miễn phí**,
> đều là tùy chọn (không cấu hình thì web vẫn chạy bình thường trên máy), và không cài thêm package Composer nào mới.

## 1. Tổng quan

| Phần | Công nghệ chính | Chi phí |
|---|---|---|
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

## 3. Lưu file upload trên Cloudinary

**Vấn đề:** khi đưa web lên các host miễn phí không có ổ đĩa lưu lâu dài, avatar, ảnh khóa học, tài liệu upload vào `storage/app/public` sẽ mất mỗi lần deploy hoặc restart. Ở máy cá nhân thì không bị.

**Giải pháp:** lưu file lên **Cloudinary**, dịch vụ lưu trữ và CDN cho ảnh.

- **Flysystem adapter tự viết** (`app/Filesystem/CloudinaryAdapter.php`): Laravel dùng thư viện League Flysystem 3 cho `Storage`. Adapter cài interface `FilesystemAdapter` và được đăng ký bằng `Storage::extend('cloudinary', ...)` trong `AppServiceProvider`. Không dùng SDK vì gói `cloudinary-labs/cloudinary-laravel` chưa hỗ trợ Laravel 13.
- **Cloudinary Upload API**: gửi file bằng Laravel HTTP Client, tham số được **ký bằng SHA-1** (sắp xếp tham số, nối với API secret) theo chuẩn của Cloudinary.
- Ảnh (`jpg`, `png`, `webp`...) upload dạng `image`. File khác (`pdf`, `docx`, `zip`...) upload dạng `raw`.
- Đường dẫn Laravel (vd. `courses/thumbnails/abc.jpg`) được giữ làm public ID. Vì vậy dữ liệu trong database không đổi, và URL ảnh (`https://res.cloudinary.com/...`) được tính ra mà không cần gọi API.
- **Tự chuyển đổi:** disk `public` chỉ dùng Cloudinary khi có biến `CLOUDINARY_URL`. Không có biến này (vd. chạy trên máy) thì vẫn lưu vào `storage/app/public` như bình thường.

File liên quan: `app/Filesystem/CloudinaryAdapter.php`, `config/filesystems.php`, `app/Providers/AppServiceProvider.php`.

## 4. Đăng nhập Google và ảnh đại diện

- **Laravel Socialite** với **Google OAuth 2.0** để đăng nhập bằng tài khoản Google.
- Lấy ảnh đại diện Google cho người dùng chưa có avatar riêng, và không ghi đè avatar người dùng tự upload.
- Ảnh từ Google được hiển thị với `referrerpolicy="no-referrer"` để Google không chặn ảnh.
- Form đổi avatar dùng **Alpine.js** để chặn bấm gửi hai lần.

File liên quan: `app/Http/Controllers/Auth/GoogleController.php`, các view có avatar, `resources/views/profile/partials/update-profile-information-form.blade.php`.

## 5. Chat AI tư vấn khóa học

Khung chat nổi trên các trang công khai, trả lời câu hỏi về khóa học dựa trên dữ liệu thật trong database.

- **Google Gemini API** (endpoint REST `generateContent`, bản `v1beta`), gọi bằng **Laravel HTTP Client**, không cài thêm package. Model chọn bằng biến `GEMINI_MODEL` (mặc định `gemini-3.5-flash-lite`).
- **Prompt (system instruction)**: gửi kèm danh sách tối đa 80 khóa học đã xuất bản (ưu tiên khóa đông học viên). Danh sách này được **cache** để không truy vấn database mỗi câu hỏi. Kèm tối đa 8 tin nhắn gần nhất để AI hiểu ngữ cảnh.
- **Giới hạn lượt hỏi** bằng **Laravel Rate Limiter**: 5 câu/phút và 30 câu/ngày cho mỗi người dùng (hoặc mỗi IP nếu chưa đăng nhập), để không vượt hạn mức miễn phí của Gemini.
- **Giao diện**: Blade partial + JavaScript thuần (`fetch` kèm CSRF token). Lịch sử chat lưu trong `sessionStorage`. Câu trả lời được hiển thị bằng `textContent` (không dùng `innerHTML`) để chống chèn mã độc (XSS).
- Không có `GEMINI_API_KEY` thì khung chat tự ẩn. Không bật thanh toán trên Google nên khi hết lượt, API chỉ báo lỗi 429 chứ không tính tiền.

File liên quan: `app/Services/GeminiClient.php`, `app/Services/CourseAdvisor.php`, `app/Services/GeminiException.php`, `app/Http/Controllers/CourseChatController.php`, `resources/views/partials/course-chat.blade.php`, route `POST /chat/courses`.

## 6. Trang Ảnh Cloudinary trong admin

Menu **Ảnh Cloudinary** (đường dẫn `/admin/cloudinary`) cho admin xem toàn bộ ảnh website đã lưu trên Cloudinary.

- **Cloudinary Admin API**: `GET resources/image/upload` lọc theo thư mục dự án (`prefix`), xác thực **HTTP Basic** bằng API key và API secret. Mỗi lần lấy tối đa 500 ảnh, lấy tiếp bằng `next_cursor`. Endpoint `usage` cho biết số credits đã dùng của gói Free.
- **Laravel Cache**: lưu danh sách 5 phút để chuyển trang, lọc không phải gọi lại API (gói Free giới hạn 500 lần gọi Admin API mỗi giờ). Nút **Làm mới** xóa cache.
- **Phân trang thủ công** bằng `LengthAwarePaginator` (24 ảnh mỗi trang), lọc theo thư mục và tìm theo tên file.
- **Cloudinary Transformations**: ảnh nhỏ được Cloudinary tạo sẵn qua URL (`c_fill,w_400,h_300/f_auto,q_auto`) nên trang tải nhanh và tốn ít băng thông.
- **Alpine.js** cho cửa sổ xem ảnh lớn, **Clipboard API** cho nút sao chép link (có cách dự phòng khi trình duyệt chặn).
- **Gắn / tháo ảnh**: mỗi ảnh cho biết đang dùng ở đâu (banner, ảnh bìa, thư viện ảnh khóa học, avatar, đính kèm) hoặc "Chưa dùng". Admin gắn ảnh có sẵn vào banner, ảnh bìa khóa học, avatar (chỉ hiện mục đang thiếu ảnh), hoặc **Tháo** ảnh khỏi một chỗ mà vẫn giữ file trên Cloudinary. Chỉ sửa đường dẫn trong database.
- **Thùng rác 7 ngày**: nút **Xóa** tháo ảnh khỏi mọi chỗ đang dùng rồi đưa vào bảng `cloudinary_trash` (ghi lại các chỗ cũ để **Khôi phục**). Hết 7 ngày, hoặc khi admin bấm **Xóa vĩnh viễn**, website gọi **Cloudinary Upload API `destroy`** (có ký SHA-1, `invalidate=true` để xóa cả bản cache trên CDN) để xóa ảnh thật trên Cloudinary.
- **Tự dọn không cần cron**: ảnh hết hạn được xóa khi admin mở trang Ảnh Cloudinary / Thùng rác, hoặc chạy tay `php artisan cloudinary:purge-trash`.
- **Chọn nhiều ảnh**: mỗi ảnh có ô tích (Alpine.js). Tích 1 ô là hiện thanh **Chọn tất cả** (trang này, rồi "chọn luôn cả các trang khác") để **xóa hàng loạt** vào thùng rác; trong Thùng rác thì **khôi phục** hoặc **xóa vĩnh viễn** hàng loạt. Danh sách đã chọn gửi lên dạng một chuỗi JSON để chọn hàng nghìn ảnh cũng không vướng giới hạn `max_input_vars` của PHP.
- **Giới hạn thời gian khi xóa vĩnh viễn**: Cloudinary xóa từng ảnh một, còn PHP thường dừng request sau 60 giây (`max_execution_time`), nên mỗi lần bấm chỉ xóa trong khoảng 40 giây (tự dọn khi mở trang: 15 giây). Ảnh chưa kịp xóa vẫn nằm trong thùng rác, bấm lại để xóa tiếp.

File liên quan: `app/Services/CloudinaryImageLibrary.php`, `app/Services/CloudinaryImageAssigner.php`, `app/Services/CloudinaryTrashBin.php`, `app/Models/CloudinaryTrash.php`, `app/Console/Commands/PurgeCloudinaryTrashCommand.php`, `app/Http/Controllers/Admin/CloudinaryController.php`, `app/Http/Controllers/Admin/CloudinaryTrashController.php`, `resources/views/admin/cloudinary/` (`index`, `trash`, `partials/tabs`, `partials/selection-bar`), `routes/admin.php`, `resources/views/layouts/admin.blade.php`.

## 7. Xác thực email và quên mật khẩu bằng mã OTP

Tài khoản đăng ký bằng email phải nhập mã 6 số gửi về email thì mới dùng được web. Quên mật khẩu cũng nhận mã 6 số qua email thay cho link.

- **Brevo Transactional API** (`POST https://api.brevo.com/v3/smtp/email`, header `api-key`): gửi qua HTTPS (cổng 443) nên vẫn chạy khi mạng hoặc host chặn cổng SMTP 25/465/587. Gói Free 300 mail/ngày, không cần thẻ.
- **Driver mail tự viết** `brevo` (`app/Mail/Transport/BrevoTransport.php`): một Symfony Mailer transport gọi API bằng **Laravel HTTP Client**, đăng ký bằng `Mail::extend()`. Không cài thêm package. Chỉ cần đổi `MAIL_MAILER` là chuyển giữa `log` (mặc định ở máy cá nhân) và `brevo` (gửi mail thật).
- **Mã OTP** (`app/Services/EmailOtpService.php`, bảng `email_otps`): mã 6 số tạo bằng `random_int`, chỉ lưu bản băm (`Hash::make`). Hết hạn sau 10 phút, sai 5 lần phải xin mã mới, gửi lại cách nhau 60 giây, tối đa 5 mã/giờ cho mỗi tài khoản (**Laravel Rate Limiter**). Cấu hình trong `config/otp.php`.
- **Xác thực email**: `User` implements `MustVerifyEmail`, ghi đè `sendEmailVerificationNotification()` để gửi mã thay cho link. Middleware `EnsureEmailIsVerifiedWithOtp` (gắn vào nhóm `web`) đưa tài khoản chưa xác thực về trang nhập mã. Đăng nhập Google, giảng viên do admin tạo và mọi tài khoản có trước tính năng này được coi là đã xác thực. Đổi email trong hồ sơ không cần xác thực lại.
- **Quên mật khẩu** (3 bước: nhập email → nhập mã → tạo mật khẩu mới): trang tạo mật khẩu mới chỉ mở sau khi nhập đúng mã, và chỉ trong 10 phút (ghi nhận trong session, `NewPasswordController`). Email lấy từ session chứ không lấy từ form. Luôn trả lời giống nhau dù email có tồn tại hay không (nhập mã cho email lạ cũng báo "Mã không đúng"), để người lạ không dò được email đã đăng ký.
- **Giao diện**: ô nhập 6 số và nút "Gửi lại mã" có đếm ngược làm bằng **Alpine.js** (dán cả mã một lần được). Mail HTML viết bằng Blade, CSS inline.
- **Chạy local**: để `MAIL_MAILER=log` thì mã OTP nằm trong `storage/logs/laravel.log` và hiện ở trang Admin > Email đã gửi. Brevo lỗi thì mã vẫn dùng được và cũng được ghi vào log (tìm chữ `Brevo`). Đặt `EMAIL_OTP_ENABLED=false` để tắt hẳn bước nhập mã.
- **Trang admin "Email đã gửi"** (`/admin/email-logs`, `EmailLogController`, bảng `email_logs`): mỗi lần gửi mã được ghi lại là **Thành công** hay **Thất bại**, gửi qua Brevo hay chỉ ghi log, kèm lỗi Brevo trả về và gợi ý cách sửa (sai API key, chặn IP, chưa xác minh người gửi, hết lượt...). Lọc theo trạng thái, loại mail, tìm theo email. Không lưu mã OTP. Nhật ký giữ 90 ngày, tự dọn khi admin mở trang (không cần cron).

Cài Brevo: tạo tài khoản free ở brevo.com, vào Settings > Senders, Domains, IPs > **Add a sender** để xác nhận Gmail dùng làm địa chỉ gửi, vào Settings > SMTP & API > **API Keys & MCP** để tạo key, rồi vào Settings > Security > Authorized IPs bấm **Deactivate blocking** (IP của máy gửi có thể thay đổi). Gửi từ Gmail mà không có tên miền riêng thì mail có thể vào mục Spam.

File liên quan: `app/Mail/Transport/BrevoTransport.php`, `app/Mail/EmailOtpMail.php`, `app/Services/EmailOtpService.php`, `app/Services/EmailOtpResult.php`, `app/Models/EmailOtp.php`, `app/Models/EmailLog.php`, `app/Http/Controllers/Admin/EmailLogController.php`, `resources/views/admin/email-logs/`, `app/Http/Middleware/EnsureEmailIsVerifiedWithOtp.php`, `app/Http/Controllers/Auth/` (`EmailVerification*`, `VerifyEmailController`, `PasswordResetLinkController`, `PasswordResetCodeController`, `NewPasswordController`, `GoogleController`), `config/otp.php`, `config/mail.php`, `resources/views/auth/` (`verify-email`, `forgot-password`, `reset-password`, `new-password`), `resources/views/components/` (`otp-input`, `otp-resend-button`, `password-reset-steps`), `resources/views/emails/`, `routes/auth.php`.

## 8. Seed dữ liệu mẫu an toàn

Seeder dùng `updateOrCreate` nên chạy lại sẽ ghi đè ảnh banner, avatar, ảnh khóa học, mật khẩu mẫu mà admin đã sửa.
Lệnh `php artisan app:seed-if-empty` (`app/Console/Commands/SeedIfEmptyCommand.php`) chỉ chạy seeder khi database chưa có người dùng nào,
nên có thể gọi nhiều lần mà không mất dữ liệu đã sửa. Muốn seed lại từ đầu thì vẫn dùng `php artisan migrate:fresh --seed` như cũ.

## 9. Kiểm thử và chất lượng code

- **PHPUnit** (feature test của Laravel), database SQLite trong bộ nhớ khi test.
- **`Http::fake()`** giả lập Cloudinary, Gemini và Brevo, **`Mail::fake()`** bắt mail OTP, nên test chạy không cần mạng và không tốn lượt API.
- **Laravel Pint** để định dạng code theo chuẩn của Laravel.
- Các file test: `tests/Feature/CloudinaryStorageTest.php`, `tests/Feature/CourseChatTest.php`, `tests/Feature/Admin/CloudinaryImagesTest.php`, `tests/Feature/Admin/CloudinaryAssignImageTest.php`, `tests/Feature/Admin/CloudinaryTrashTest.php`, `tests/Feature/Admin/CloudinaryBulkDeleteTest.php`, `tests/Feature/Auth/GoogleAuthenticationTest.php`, `tests/Feature/Auth/EmailVerificationTest.php`, `tests/Feature/Auth/PasswordResetTest.php`, `tests/Feature/Mail/BrevoTransportTest.php`, `tests/Feature/Admin/EmailLogTest.php`, `tests/Feature/SeedIfEmptyCommandTest.php`.

Chạy test:

```bash
php artisan test
```

## 10. Biến môi trường

| Biến | Dùng cho | Lấy ở đâu |
|---|---|---|
| `APP_KEY` | Mã hóa session, cookie | `php artisan key:generate --show` |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Kết nối MySQL | MySQL trên máy (XAMPP, Laragon...) |
| `CLOUDINARY_URL` | Lưu file lên Cloudinary | Cloudinary Console > API Keys > API environment variable |
| `CLOUDINARY_FOLDER` | Thư mục gốc trên Cloudinary (mặc định `online-elearning`) | Tự đặt |
| `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` | Đăng nhập Google | Google Cloud Console > Credentials |
| `GEMINI_API_KEY` | Chat AI | https://aistudio.google.com/apikey |
| `GEMINI_MODEL` | Tên model Gemini | Mặc định `gemini-3.5-flash-lite` |
| `MAIL_MAILER` | Cách gửi mail: `log` (mặc định, chỉ ghi log) hoặc `brevo` (gửi thật) | Tự đặt |
| `BREVO_API_KEY` | Gửi mail qua Brevo | Brevo > Settings > SMTP & API > API Keys & MCP |
| `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | Địa chỉ và tên người gửi | Địa chỉ đã xác nhận trong Brevo > Senders |
| `EMAIL_OTP_ENABLED` | Bật/tắt bước nhập mã OTP khi đăng ký (mặc định bật) | Tự đặt `true` / `false` |

## 11. Giữ chi phí bằng 0

- **Cloudinary Free**: tính theo credits mỗi tháng. Trang Ảnh Cloudinary hiện số credits đã dùng để theo dõi.
- **Gemini Free**: không bật thanh toán, hết lượt thì chỉ báo lỗi, không bị trừ tiền.
- **Brevo Free**: 300 mail/ngày, không nhập thẻ. Web giới hạn mỗi tài khoản 5 mã/giờ nên khó chạm mức này.
- **Database**: MySQL chạy trên máy, không tốn phí.
