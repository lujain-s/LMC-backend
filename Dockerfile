FROM php:8.2-fpm

# تثبيت الباكدجات المطلوبة
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath curl

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# إنشاء مجلد التطبيق
WORKDIR /var/www

# نسخ كل الملفات
COPY . .

# تثبيت dependecies Laravel
RUN composer install --no-dev --optimize-autoloader

# إعداد صلاحيات التخزين
RUN chown -R www-data:www-data /var/www/storage

# فتح البورت
EXPOSE 8000

# نسخ ملفات السكربتات
COPY start.sh /start.sh

# إعطاء صلاحيات التشغيل
RUN chmod +x /start.sh /init.sh

# عند تشغيل الحاوية، يبدأ التطبيق فقط (بدون seed/migrate)
CMD ["/start.sh"]

