FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-progress --optimize-autoloader --no-scripts

FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install --ignore-scripts || true
COPY resources ./resources
COPY public ./public
COPY vite.config.js* ./
RUN if [ -f vite.config.js ]; then npm run build; else mkdir -p public/build && echo '{}' > public/build/manifest.json; fi

FROM php:8.4-cli
WORKDIR /var/www

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build
COPY . .

COPY entrypoint.sh /usr/local/bin/geoeco-entrypoint

RUN mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache \
    && chmod +x /usr/local/bin/geoeco-entrypoint

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/geoeco-entrypoint"]