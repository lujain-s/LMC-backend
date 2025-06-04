#!/bin/bash

echo "📦 تشغيل migrate و seeder..."

# تشغيل migrate
php artisan migrate --force

# تشغيل seeders
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan db:seed --class=LMCInfoSeeder

echo "✅ تم التهيئة بنجاح!"
