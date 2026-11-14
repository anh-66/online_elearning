# Lấy Brevo API key (gửi email thật: mã OTP, đặt lại mật khẩu)

Project gửi email ở các chỗ: mã OTP xác thực email khi đăng ký, mã đặt lại
mật khẩu, và các thông báo khác.

Mặc định ở máy dùng `MAIL_MAILER=log`: **không gửi mail thật**, nội dung mail
(kể cả mã OTP) được ghi vào `storage/logs/laravel.log` và hiện ở trang
**Admin > Email đã gửi** (`/admin/email-logs`). Như vậy là đủ để test.

Chỉ cần Brevo khi muốn mail tới hộp thư thật. Gói **Free** của Brevo: 300 email
mỗi ngày, không cần thẻ thanh toán.

## Biến cần điền

```env
MAIL_MAILER=brevo
BREVO_API_KEY=xkeysib-...
MAIL_FROM_ADDRESS="email-da-xac-thuc-tren-brevo@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

`MAIL_FROM_ADDRESS` **bắt buộc** là email đã xác thực ở bước 3, nếu không Brevo
sẽ từ chối gửi. Các biến `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`,
`MAIL_PASSWORD` không dùng cho Brevo, để nguyên.

## Các bước

### 1. Tạo tài khoản

1. Vào <https://www.brevo.com/> bấm **Sign up free**, đăng ký bằng email hoặc
   Google.
2. Điền thông tin công ty (ghi tên nhóm/trường là được), chọn gói **Free**.
3. Xác thực email đăng ký. Brevo có thể yêu cầu xác thực số điện thoại trước
   khi cho gửi mail.

### 2. Tạo API key

1. Bấm tên tài khoản ở góc trên bên phải > **SMTP & API**
   (hoặc **Settings > SMTP & API**).
2. Chọn tab **API Keys** (có thể tên là **API Keys & MCP**), bấm
   **Generate a new API key**.
3. Đặt tên (ví dụ `online-elearning-local`), bấm **Generate**.
4. Copy key ngay, key bắt đầu bằng `xkeysib-`. Brevo chỉ hiện key một lần.

Chú ý: dùng **API key** (`xkeysib-…`), không phải **SMTP key** (`xsmtpsib-…`)
ở tab bên cạnh. Project gọi API HTTP của Brevo chứ không dùng SMTP.

### 3. Xác thực email người gửi

1. Vào **Settings > Senders, Domains & Dedicated IPs > Senders**.
2. Bấm **Add a sender**, điền tên hiển thị và email (Gmail của bạn được).
3. Brevo gửi mã hoặc link xác thực vào email đó. Mở hộp thư và xác nhận.
4. Dùng đúng email này cho `MAIL_FROM_ADDRESS`.

Gửi bằng Gmail mà không có tên miền riêng thì mail dễ rơi vào **Spam**. Khi
test, nhớ kiểm tra cả thư mục Spam / Quảng cáo.

### 4. Cho phép gửi từ máy của bạn (Authorized IPs)

Brevo mặc định có thể chặn API key được gọi từ IP lạ và trả lỗi `401`. Mạng nhà
hay mạng trường thường đổi IP, nên cách đơn giản nhất khi test:

1. Vào **Settings > Security > Authorized IPs**.
2. Bấm **Deactivate blocking** (tắt chặn IP lạ).
   Hoặc bấm **Add IP** và thêm IP hiện tại của bạn (xem ở
   <https://api.ipify.org>), nhưng IP đổi thì phải thêm lại.

### 5. Điền vào `.env`

```env
MAIL_MAILER=brevo
BREVO_API_KEY=<key xkeysib- vừa copy>
MAIL_FROM_ADDRESS="<email đã xác thực ở bước 3>"
MAIL_FROM_NAME="${APP_NAME}"
EMAIL_OTP_ENABLED=true
```

```bash
php artisan config:clear
```

### 6. Thử

1. Mở `http://localhost:8000/register`, đăng ký bằng một email thật của bạn.
2. Mã OTP 6 số sẽ tới hộp thư (mã hết hạn sau 10 phút).
3. Đăng nhập admin, vào `/admin/email-logs` để xem trạng thái thật của mail,
   kèm lý do và gợi ý sửa nếu lỗi (xem mục dưới).

## Trạng thái thật của mail

Brevo trả lời "đã nhận" ngay khi web gửi, vài giây sau mới giao tới hộp thư hoặc
từ chối. Web tự hỏi lại Brevo (API `GET /v3/smtp/statistics/events`, dùng được
với gói Free, không cần webhook nên chạy được ở local) và hiện đúng trạng thái:

| Nhãn | Ý nghĩa |
|---|---|
| Chờ xác nhận | Brevo đã nhận, chưa báo giao hay chưa. Thường chỉ vài giây. |
| Đã tới hộp thư / Đã tới, đã mở | Mail đã tới máy chủ hộp thư người nhận (có thể nằm trong Spam). |
| Đang thử lại | Hộp thư người nhận tạm từ chối, Brevo đang gửi lại. |
| Bị từ chối | Brevo không giao được: địa chỉ gửi chưa xác thực, email người nhận sai, bị chặn... |
| Thất bại | Web không gửi được tới Brevo: sai key, sai IP, hết lượt, mất mạng... |

Trạng thái được cập nhật khi:

- Admin mở trang `/admin/email-logs` (hỏi 10 mail mới nhất đang chờ), hoặc bấm
  nút **Cập nhật trạng thái**.
- Người dùng đang ở trang nhập mã: trang tự hỏi và báo "Mail đã tới hộp thư"
  hoặc lý do không tới.
- Chạy lệnh `php artisan email-logs:sync` (có thể đặt vào lịch chạy nếu muốn).

Brevo chỉ giữ sự kiện vài ngày, nên mail cũ hơn 3 ngày mà chưa có kết quả sẽ hiện
"Không rõ".

## Tắt OTP hoặc quay lại không gửi mail

- Không muốn bước nhập mã khi đăng ký: `EMAIL_OTP_ENABLED=false`.
- Quay lại chế độ ghi log: `MAIL_MAILER=log`.

Sửa xong nhớ chạy `php artisan config:clear`.

## Lỗi hay gặp

Trang `/admin/email-logs` hiện lý do cụ thể cho từng mail lỗi. Các lỗi thường
gặp:

| Hiện tượng | Nguyên nhân và cách sửa |
|---|---|
| "Chưa có BREVO_API_KEY" | Chưa điền key hoặc chưa chạy `php artisan config:clear`. |
| `401` + `unrecognised IP address` | Brevo chặn IP máy bạn. Làm bước 4. |
| `401` + `Key not found` | Key sai, bị xóa, hoặc đang dùng nhầm SMTP key `xsmtpsib-`. Tạo API key mới ở bước 2. |
| `400` + `sender ... not valid` | `MAIL_FROM_ADDRESS` chưa được xác thực trong Brevo. Làm bước 3. |
| `402` | Hết lượt gói Free (300 mail/ngày) hoặc tài khoản chưa được Brevo kích hoạt gửi mail. Đợi sang ngày mới, hoặc xem thông báo trong trang Brevo. |
| Gửi thành công nhưng không thấy mail | Xem thư mục Spam. Kiểm tra thêm ở Brevo > **Transactional > Logs**. |
| Đăng ký báo gửi quá nhiều mã | Gửi lại phải chờ tăng dần 1, 2, 5 rồi 10 phút; mỗi tài khoản / email tối đa 5 mã mỗi giờ; mỗi mạng (IP) tối đa 20 lần xin mã mỗi giờ. Đợi rồi bấm gửi lại. |
| "Hôm nay hệ thống đã gửi quá nhiều email" | Đã gửi đủ `EMAIL_OTP_MAX_PER_DAY` mail OTP trong ngày (mặc định 250, để dưới hạn 300 mail/ngày của gói Free). Tăng số này trong `.env` nếu cần. |
| Trạng thái đứng ở "Chờ xác nhận" | Trang admin hiện lỗi đọc trạng thái ở phía trên (thường là key sai hoặc IP chưa được cho phép). Sửa xong bấm **Cập nhật trạng thái**. |
