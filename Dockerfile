FROM php:8.4-fpm

# 1. Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev libicu-dev nodejs npm

# 2. Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Tambahkan ini untuk konfigurasi PHP upload
RUN { \
    echo "upload_max_filesize = 64M"; \
    echo "post_max_size = 64M"; \
    echo "memory_limit = 512M"; \
    echo "max_execution_time = 300"; \
    } > /usr/local/etc/php/conf.d/uploads.ini

# 3. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# 4. Install dependencies
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs
RUN npm install && npm run build

# 5. Set Permissions untuk Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

