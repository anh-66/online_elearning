# Lấy Cloudinary URL (lưu ảnh và file upload lên cloud)

Khi có `CLOUDINARY_URL`, mọi file upload qua disk `public` (ảnh khóa học,
avatar, banner, danh mục, tài liệu bài học…) được đưa lên Cloudinary thay vì lưu ở
`storage/app/public`. Đồng thời trang **Admin > Ảnh Cloudinary**
(`/admin/cloudinary`) dùng được để xem dung lượng, gán ảnh cho khóa học, và
thùng rác ảnh (`/admin/cloudinary/trash`).

Không có key thì project vẫn chạy, file lưu trên máy như bình thường.

Gói **Free** của Cloudinary: 25 credit mỗi tháng (khoảng 25 GB lưu trữ hoặc
băng thông), không cần thẻ thanh toán.

## Biến cần điền

```env
CLOUDINARY_URL=cloudinary://<API_KEY>:<API_SECRET>@<CLOUD_NAME>
CLOUDINARY_FOLDER=online-elearning
```

Trong `.env.example` hai dòng này đang có dấu `#` ở đầu. Bỏ dấu `#` rồi điền.

`CLOUDINARY_FOLDER` là thư mục chứa file trên Cloudinary. Nhiều người dùng
chung một tài khoản Cloudinary thì mỗi người đặt một tên riêng (ví dụ
`online-elearning-an`) để không đè ảnh của nhau.

## Các bước

1. Vào <https://cloudinary.com/users/register_free> và đăng ký (đăng nhập bằng
   Google hoặc GitHub cũng được). Gói mặc định là **Free**.
2. Đăng nhập xong sẽ vào **Console**. Mở **Settings** (biểu tượng bánh răng ở
   góc dưới bên trái) > **API Keys**. Trang **Dashboard** cũng có ô "API
   Environment variable".
3. Ở trên cùng có dòng **API environment variable** dạng:

   ```text
   CLOUDINARY_URL=cloudinary://123456789012345:**********@your-cloud-name
   ```

   Bấm biểu tượng con mắt ở cột **API Secret** của key để hiện secret, rồi bấm
   nút copy của dòng **API environment variable**. Nếu chuỗi copy ra vẫn còn
   `<your_api_secret>` thì thay phần đó bằng secret thật.
4. Dán vào `.env` (bỏ dấu `#`):

   ```env
   CLOUDINARY_URL=cloudinary://123456789012345:abcDEF...@your-cloud-name
   CLOUDINARY_FOLDER=online-elearning
   ```

5. Chạy:

   ```bash
   php artisan config:clear
   ```

6. Đăng nhập admin (`admin@gmail.com` / `Password123`), vào
   `http://localhost:8000/admin/cloudinary`. Thấy dung lượng đã dùng và danh
   sách ảnh là kết nối thành công. Thử tạo khóa học mới có ảnh, ảnh sẽ nằm trong
   **Media Library** của Cloudinary, thư mục `CLOUDINARY_FOLDER`.

## Lưu ý

- File đã upload trước khi bật Cloudinary vẫn nằm ở `storage/app/public`, không
  tự chuyển lên cloud. Database đang trỏ tới file trên máy thì sau khi bật
  Cloudinary ảnh đó sẽ không hiện. Cách nhanh nhất khi test: upload lại, hoặc
  vào `/admin/cloudinary` gán ảnh có sẵn trên Cloudinary cho khóa học.
- Xóa ảnh trong trang Admin chỉ chuyển vào **thùng rác** 7 ngày. Muốn xóa hẳn
  trên Cloudinary những ảnh đã quá hạn, chạy:

  ```bash
  php artisan cloudinary:purge-trash
  ```

  Hoặc bấm **Xóa vĩnh viễn** trong `/admin/cloudinary/trash`.
- Muốn quay lại lưu trên máy: thêm `#` trước dòng `CLOUDINARY_URL`, chạy
  `php artisan config:clear`.
- `API_SECRET` có quyền xóa toàn bộ ảnh. Không chia sẻ công khai.

## Lỗi hay gặp

| Hiện tượng | Nguyên nhân và cách sửa |
|---|---|
| `/admin/cloudinary` báo "chưa kết nối Cloudinary" | `CLOUDINARY_URL` còn dấu `#`, đang trống, hoặc chưa chạy `php artisan config:clear`. |
| Lỗi `CLOUDINARY_URL phải có dạng cloudinary://API_KEY:API_SECRET@CLOUD_NAME` | Chuỗi bị thiếu phần hoặc còn dấu `<` `>`. Copy lại nguyên dòng ở bước 3. |
| Upload báo lỗi, log có `401` / `Invalid Signature` / `Unknown API key` | Sai API key hoặc secret. Copy lại cả dòng ở bước 3, chú ý không còn `<your_api_secret>`. |
| Log có `Invalid cloud_name` | Phần sau dấu `@` sai tên cloud. |
| Ảnh cũ không hiện sau khi bật Cloudinary | Xem mục Lưu ý đầu tiên. |
