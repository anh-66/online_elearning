# Lấy Google OAuth Client ID (đăng nhập bằng Google)

Dùng cho nút **Đăng nhập bằng Google** ở trang `/login` và `/register`. Khi
đăng nhập lần đầu, hệ thống tự tạo tài khoản học viên và lấy avatar Google.

Miễn phí, không cần bật thanh toán.

## Biến cần điền

```env
GOOGLE_CLIENT_ID=xxxxxxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-xxxxxxxx
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

`GOOGLE_REDIRECT_URI` giữ nguyên như trong `.env.example`. Với
`APP_URL=http://localhost:8000` thì giá trị thật là
`http://localhost:8000/auth/google/callback`.

## Các bước

### 1. Tạo project trên Google Cloud

1. Vào <https://console.cloud.google.com/> và đăng nhập Gmail.
2. Bấm ô chọn project ở thanh trên cùng, chọn **New Project**.
3. Đặt tên (ví dụ `online-elearning`), bấm **Create**, rồi chọn project vừa tạo.

### 2. Cấu hình màn hình đồng ý (OAuth consent screen)

1. Menu bên trái: **APIs & Services > OAuth consent screen** (giao diện mới gọi
   là **Google Auth Platform**). Bấm **Get started**.
2. **App name**: tên bất kỳ, ví dụ `Online Elearning`. **User support email**:
   chọn Gmail của bạn.
3. **Audience**: chọn **External**.
4. **Contact information**: điền Gmail của bạn, đồng ý điều khoản, bấm **Create**.
5. Vào mục **Audience**. App đang ở chế độ **Testing**, chỉ những Gmail có trong
   danh sách **Test users** mới đăng nhập được. Bấm **Add users** và thêm Gmail
   của bạn và các thành viên nhóm (tối đa 100 người).

Không cần thêm scope nào. Project chỉ xin `openid`, `email`, `profile` là các
quyền cơ bản, Google không bắt duyệt app.

### 3. Tạo OAuth Client ID

1. Vào **APIs & Services > Credentials** (hoặc **Google Auth Platform > Clients**).
2. Bấm **Create credentials > OAuth client ID** (hoặc **Create client**).
3. **Application type**: chọn **Web application**. Đặt tên tùy ý.
4. **Authorized JavaScript origins**, bấm **Add URI** và thêm:
   - `http://localhost:8000`
   - `http://127.0.0.1:8000`
5. **Authorized redirect URIs**, bấm **Add URI** và thêm:
   - `http://localhost:8000/auth/google/callback`
   - `http://127.0.0.1:8000/auth/google/callback`
6. Bấm **Create**. Google hiện **Client ID** và **Client secret**. Copy cả hai
   ngay (bấm **Download JSON** để lưu lại cũng được). Giao diện mới của Google
   chỉ cho xem secret đầy đủ lúc vừa tạo; mất thì tạo secret mới trong trang
   chi tiết client.

Thay đổi redirect URI có thể mất vài phút mới có hiệu lực.

### 4. Điền vào `.env`

```env
GOOGLE_CLIENT_ID=<Client ID vừa copy>
GOOGLE_CLIENT_SECRET=<Client secret vừa copy>
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

```bash
php artisan config:clear
```

### 5. Thử

Mở đúng địa chỉ trong `APP_URL` (mặc định `http://localhost:8000`), vào
`/login`, bấm **Đăng nhập bằng Google**.

## Lỗi hay gặp

| Lỗi | Nguyên nhân và cách sửa |
|---|---|
| `Error 400: redirect_uri_mismatch` | URI gửi lên không khớp từng ký tự với danh sách ở bước 3. Kiểm tra `APP_URL` (http hay https, `localhost` hay `127.0.0.1`, cổng 8000) rồi thêm đúng URI đó vào Google. |
| `Missing required parameter: client_id` | Chưa điền `GOOGLE_CLIENT_ID` hoặc chưa chạy `php artisan config:clear`. |
| `Error 403: access_denied` | Gmail đó chưa có trong **Test users**. Thêm vào ở bước 2.5. |
| Quay về trang web thì báo lỗi `InvalidStateException` | Bạn mở web bằng `127.0.0.1` nhưng `APP_URL` là `localhost` (hoặc ngược lại), nên cookie phiên bị mất khi quay về. Luôn mở web bằng đúng địa chỉ trong `APP_URL`. |
| Đăng nhập được nhưng không thấy avatar Google | Avatar Google chỉ được dùng khi tài khoản chưa tự upload avatar riêng. Nếu chưa upload, đăng xuất rồi đăng nhập Google lại. |
