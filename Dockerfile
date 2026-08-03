FROM php:8.4-fpm-alpine AS base

RUN apk add --no-cache \
    postgresql-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

FROM base AS vendor
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist
COPY database/ database/
RUN composer install --no-dev --prefer-dist

FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json vite.config.js tailwind.config.js postcss.config.js ./
RUN npm ci
COPY resources/ resources/
RUN npm run build

FROM base AS final
RUN addgroup -g 1000 -S app && adduser -u 1000 -S app -G app

COPY --from=vendor /var/www/html /var/www/html
COPY --from=frontend /app/public/build /var/www/html/public/build
COPY . .

RUN php artisan storage:link \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

USER app

EXPOSE 9000
CMD ["php-fpm"]
