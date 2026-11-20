#!/bin/bash

set -e

cd /var/www/proposite

echo "1/6 Descargando el código nuevo..."
git pull --ff-only origin main

echo "2/6 Instalando dependencias de PHP..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "3/6 Instalando dependencias de JavaScript y compilando React..."
npm ci
npm run build

echo "4/6 Aplicando migraciones..."
php artisan migrate --force

echo "5/6 Generando cachés de Laravel..."
php artisan optimize

echo "6/6 Ajustando permisos de escritura..."
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache

echo "Despliegue terminado."