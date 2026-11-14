#!/usr/bin/env bash
# Chạy mỗi lần container khởi động trên Render (deploy, restart, hoặc khi service free "thức dậy").
set -euo pipefail

cd /var/www/html

if [ -z "${APP_KEY:-}" ]; then
    echo "[entrypoint] Thiếu APP_KEY. Tạo bằng 'php artisan key:generate --show' rồi thêm vào Environment trên Render." >&2
    exit 1
fi

# Render tự cấp RENDER_EXTERNAL_URL (vd: https://online-elearning.onrender.com).
# Dùng nó làm APP_URL nếu chưa đặt; tự đặt APP_URL khi dùng tên miền riêng.
if [ -z "${APP_URL:-}" ] && [ -n "${RENDER_EXTERNAL_URL:-}" ]; then
    export APP_URL="${RENDER_EXTERNAL_URL}"
fi
if [ -z "${GOOGLE_REDIRECT_URI:-}" ] && [ -n "${APP_URL:-}" ]; then
    export GOOGLE_REDIRECT_URI="${APP_URL%/}/auth/google/callback"
fi

# Secret File của Render (/etc/secrets) chỉ root đọc được, còn Apache chạy PHP bằng www-data.
# Chép file CA (là chứng chỉ công khai, không phải bí mật) ra chỗ www-data đọc được.
if [ -n "${MYSQL_ATTR_SSL_CA:-}" ] && [ -f "${MYSQL_ATTR_SSL_CA}" ]; then
    install -m 0644 "${MYSQL_ATTR_SSL_CA}" /etc/ssl/certs/mysql-ca.pem
    export MYSQL_ATTR_SSL_CA=/etc/ssl/certs/mysql-ca.pem
fi

# Link public/storage -> storage/app/public để hiển thị file upload.
php artisan storage:link --force --no-interaction >/dev/null

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    attempt=1
    max_attempts="${DB_CONNECT_RETRIES:-5}"
    # Database cloud có thể mất vài giây để nhận kết nối, nên thử lại vài lần.
    until php artisan migrate --force --no-interaction; do
        if [ "$attempt" -ge "$max_attempts" ]; then
            echo "[entrypoint] Không migrate được sau ${attempt} lần. Kiểm tra lại DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD, MYSQL_ATTR_SSL_CA." >&2
            exit 1
        fi
        echo "[entrypoint] Migrate lỗi (lần ${attempt}/${max_attempts}), thử lại sau 5 giây..." >&2
        attempt=$((attempt + 1))
        sleep 5
    done
fi

# Seed dữ liệu mẫu (tài khoản demo, khóa học, banner mẫu):
#   RUN_SEEDERS=true  -> chỉ seed khi database còn trống (lần deploy đầu). Đã có dữ liệu thì bỏ qua,
#                        để ảnh banner, avatar, ảnh khóa học... admin đã sửa không bị ghi đè khi restart.
#   RUN_SEEDERS=force -> seed lại mỗi lần khởi động (đặt lại toàn bộ dữ liệu mẫu, kể cả ảnh và mật khẩu).
case "${RUN_SEEDERS:-false}" in
    true)
        php artisan app:seed-if-empty --no-interaction
        ;;
    force)
        php artisan db:seed --force --no-interaction
        ;;
esac

# Danh sách thành tựu (huy hiệu) lấy từ AchievementSeeder. Database đã có dữ liệu thì seeder mẫu ở trên
# bỏ qua, nên nạp riêng danh sách này mỗi lần khởi động. Seeder chỉ ghi bảng achievements theo slug
# (updateOrCreate), không đụng tới tiến độ của học viên hay dữ liệu khác.
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan db:seed --class=AchievementSeeder --force --no-interaction \
        || echo "[entrypoint] Chưa nạp được danh sách thành tựu, sẽ thử lại lần sau." >&2
fi

# Xóa vĩnh viễn trên Cloudinary những ảnh nằm trong thùng rác quá 7 ngày.
# Render gói free không có cron miễn phí nên dọn mỗi lần khởi động (và khi admin mở trang Ảnh Cloudinary).
php artisan cloudinary:purge-trash --no-interaction \
    || echo "[entrypoint] Chưa dọn được thùng rác Cloudinary, sẽ thử lại lần sau." >&2

# Cache config, route, view, event cho production (đọc biến môi trường lúc chạy).
php artisan optimize --no-interaction

chown -R www-data:www-data storage bootstrap/cache

exec docker-php-entrypoint "$@"
