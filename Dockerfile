FROM php:8.4-cli

# 1. Install system dependencies (Ditambah libzip-dev agar ekstensi zip PHP bisa di-compile)
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev nodejs npm

# 2. Install PHP extensions (Ditambah 'zip')
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# 3. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# 4. Gunakan flag --ignore-platform-reqs untuk jaga-jaga beda versi PHP
RUN composer install --ignore-platform-reqs

RUN npm install && npm run build

CMD php artisan serve --host=0.0.0.0 --port=8000