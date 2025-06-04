#!/bin/bash

# تنفيذ سكربت التهيئة (بالمسار المطلق)
sh /init.sh

# بدء الخادم على البورت 8000
echo "🚀 Starting Laravel server on port 8000..."
php artisan serve --host=0.0.0.0 --port=8000
