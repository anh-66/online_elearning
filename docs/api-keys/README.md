# Hướng dẫn lấy API key

Project chạy được ở máy **mà không cần key nào**. Mỗi key chỉ bật thêm một
chức năng. Tất cả dịch vụ dưới đây đều có gói miễn phí, không cần thẻ thanh toán.

| Dịch vụ | Biến trong `.env` | Bật chức năng gì | Không có key thì sao | Hướng dẫn |
|---|---|---|---|---|
| Google OAuth | `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` | Nút "Đăng nhập bằng Google", lấy avatar Google | Bấm nút Google sẽ báo lỗi, đăng nhập bằng email vẫn bình thường | [google-oauth.md](google-oauth.md) |
| Google Gemini | `GEMINI_API_KEY`, `GEMINI_MODEL` | Khung chat AI tư vấn khóa học ở trang public | Khung chat bị ẩn | [gemini.md](gemini.md) |
| Cloudinary | `CLOUDINARY_URL`, `CLOUDINARY_FOLDER` | Lưu ảnh/file upload lên cloud, trang Admin > Ảnh Cloudinary | File lưu ở `storage/app/public` trên máy | [cloudinary.md](cloudinary.md) |
| Brevo | `MAIL_MAILER=brevo`, `BREVO_API_KEY`, `MAIL_FROM_ADDRESS` | Gửi email thật (mã OTP, đặt lại mật khẩu) | Mail ghi vào `storage/logs/laravel.log` và trang Admin > Email đã gửi | [brevo.md](brevo.md) |

Cách chạy project từ đầu: xem [../huong-dan-chay-local.md](../huong-dan-chay-local.md).

## Quy tắc chung

- Chỉ dán key vào file `.env`. Không dán vào `.env.example`, code, ảnh chụp màn
  hình hay tin nhắn nhóm. File `.env` đã nằm trong `.gitignore`.
- Sau mỗi lần sửa `.env`, chạy:

  ```bash
  php artisan config:clear
  ```

  Nếu đang chạy `php artisan serve` thì tắt đi chạy lại cho chắc.
- Mỗi thành viên nên tự tạo key riêng bằng tài khoản của mình, để hết lượt miễn
  phí của người này không ảnh hưởng người khác.
- Lỡ để lộ key: vào trang quản lý của dịch vụ đó xóa key cũ, tạo key mới.
