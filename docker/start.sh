#!/bin/sh
set -e

cd /var/www/html

# Railway cấp cổng qua biến PORT
PORT="${PORT:-8080}"
sed -ri "s/Listen [0-9]+/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Volume (nếu có) được gắn lúc chạy nên cần tạo thư mục và cấp quyền ở đây
mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
chown -R www-data:www-data storage bootstrap/cache

php artisan storage:link --force || true
php artisan migrate --force
php artisan app:seed-if-empty
php artisan optimize

exec apache2-foreground
