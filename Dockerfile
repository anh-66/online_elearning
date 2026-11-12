# syntax=docker/dockerfile:1

# =============================================================================
# Image chạy EduLearn (Laravel 13) trên Render.
#   - Stage "base":   PHP 8.4 + Apache + các extension Laravel cần.
#   - Stage "vendor": cài dependency PHP (không có gói dev).
#   - Stage "assets": build CSS/JS bằng Vite.
#   - Stage "app":    image cuối cùng, Apache nghe ở cổng $PORT (Render mặc định 10000).
# =============================================================================

FROM php:8.4-apache-bookworm AS base

COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql gd zip intl exif opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html


FROM base AS vendor

COPY composer.json composer.lock ./
# composer.json đang bật "disable-tls"; chỉ tắt trong lúc build để tải package qua HTTPS.
RUN composer config disable-tls false \
    && composer install --no-dev --no-interaction --no-progress --prefer-dist --no-scripts --no-autoloader


FROM node:22-bookworm-slim AS assets

WORKDIR /app
COPY package.json package-lock.json .npmrc ./
RUN npm ci --no-audit --no-fund

COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
# Tailwind quét view phân trang của Laravel để giữ lại các class CSS của nó.
COPY --from=vendor /var/www/html/vendor/laravel/framework/src/Illuminate/Pagination/resources/views ./vendor/laravel/framework/src/Illuminate/Pagination/resources/views
RUN npm run build


FROM base AS app

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    PORT=10000

# Apache: DocumentRoot = public/, bật mod_rewrite, nghe ở cổng $PORT.
COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite headers \
    && sed -i 's/^Listen 80$/Listen ${PORT}/' /etc/apache2/ports.conf \
    && echo 'ServerName localhost' > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php/app.ini "$PHP_INI_DIR/conf.d/zz-app.ini"

COPY --from=vendor /var/www/html/vendor ./vendor
COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --no-dev --optimize --no-interaction \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x docker/entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["docker/entrypoint.sh"]
CMD ["apache2-foreground"]
