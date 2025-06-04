#!/bin/bash

# تشغيل migrate مع تجاهل الفشل (اختياري)
php artisan migrate --force || true

# تشغيل seeders
php artisan db:seed --class=RolesAndPermissionsSeeder || true
php artisan db:seed --class=LMCInfoSeeder || true

# تشغيل السيرفر (يفضل Nginx أو php-fpm في production، وليس artisan serve)
php-fpm
