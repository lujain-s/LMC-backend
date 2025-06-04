#!/bin/bash

echo "📦 تشغيل migrate و seeder..."

# تشغيل migrate
php artisan migrate --force

# تشغيل seeders
php artisan db:seed --class=RolesAndPermissionsSeeder --force
php artisan db:seed --class=LMCInfoSeeder --force


echo "✅ تم التهيئة بنجاح!"
