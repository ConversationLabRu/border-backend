# Используем PHP-FPM
FROM php:8.1-fpm

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Копируем файлы проекта
COPY . .

# Устанавливаем необходимые зависимости
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Устанавливаем зависимости через Composer
RUN composer install

# Копируем конфигурацию Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Экспонируем порт 80
EXPOSE 80

# Запускаем Nginx и PHP-FPM
CMD ["sh", "-c", "php-fpm & nginx -g 'daemon off;'"]
