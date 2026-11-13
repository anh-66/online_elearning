# Lấy Google Gemini API key (chat AI tư vấn khóa học)

Dùng cho khung **chat AI** ở góc màn hình các trang public (trang chủ, danh
sách khóa học…). AI đọc danh sách khóa học trong database rồi gợi ý cho người
dùng. Chưa có key thì khung chat tự ẩn, các trang khác vẫn chạy bình thường.

Dùng gói **miễn phí** của Google AI Studio, không cần thẻ thanh toán.

## Biến cần điền

```env
GEMINI_API_KEY=AIzaSy...
GEMINI_MODEL=gemini-3.5-flash-lite
```

`GEMINI_MODEL` để mặc định là được. Chỉ đổi khi Google ngừng model đó.

## Các bước

1. Vào <https://aistudio.google.com/apikey> và đăng nhập Gmail.
2. Lần đầu vào, đồng ý điều khoản của Google AI Studio.
3. Bấm **Create API key**. Chọn một Google Cloud project có sẵn (ví dụ project
   đã tạo cho Google OAuth) hoặc để AI Studio tự tạo project mới.
4. Copy key (bắt đầu bằng `AIza`).
5. Dán vào `.env`:

   ```env
   GEMINI_API_KEY=<key vừa copy>
   ```

6. Chạy:

   ```bash
   php artisan config:clear
   ```

7. Mở trang chủ `http://localhost:8000`, thấy nút chat ở góc dưới, hỏi thử
   "Có khóa học nào về PHP không?".

## Giữ ở mức miễn phí

- **Không** bấm "Set up billing" / "Upgrade" trong AI Studio. Khi chưa bật
  thanh toán, Google chỉ chặn khi hết lượt chứ không tính tiền.
- Project tự giới hạn mỗi người dùng (hoặc mỗi IP nếu chưa đăng nhập)
  **5 câu/phút** và **30 câu/ngày**, để cả nhóm không đốt hết lượt miễn phí.
- Ở gói miễn phí, Google có thể dùng nội dung chat để cải thiện sản phẩm. Đừng
  gõ thông tin cá nhân thật khi thử.

## Đổi model

Danh sách model và giới hạn miễn phí xem ở <https://aistudio.google.com/>
(mục chọn model) hoặc <https://ai.google.dev/gemini-api/docs/models>. Chọn model
có chữ `flash` hoặc `flash-lite` cho nhanh và nhiều lượt miễn phí, rồi sửa:

```env
GEMINI_MODEL=<tên model>
```

```bash
php artisan config:clear
```

## Lỗi hay gặp

Khi chat trả lời "AI đang gặp sự cố…" hoặc "AI đang quá tải…", mở
`storage/logs/laravel.log`, tìm dòng `Chat AI tư vấn khóa học lỗi:` để xem lỗi
thật từ Google.

| Hiện tượng | Nguyên nhân và cách sửa |
|---|---|
| Không thấy khung chat | `GEMINI_API_KEY` đang trống hoặc chưa chạy `php artisan config:clear`. |
| Chat trả lời "Bạn gửi hơi nhanh…" / "hết 30 câu hỏi hôm nay" | Đây là giới hạn của project, không phải lỗi. Đợi 1 phút hoặc sang ngày mới. Khi thử nhiều có thể chạy `php artisan cache:clear` để reset. |
| Log có `HTTP 400` + `API key not valid` | Key sai hoặc đã bị xóa. Tạo key mới ở AI Studio. |
| Log có `HTTP 404` + `is not found` | Model trong `GEMINI_MODEL` không còn. Đổi sang model khác như mục trên. |
| Log có `HTTP 429` / `RESOURCE_EXHAUSTED` | Hết lượt miễn phí của Google trong phút/ngày. Đợi rồi thử lại, hoặc dùng key của thành viên khác. |
| Log có `Không kết nối được Gemini API` | Máy không ra được Internet hoặc mạng chặn `generativelanguage.googleapis.com`. Thử mạng khác. |
