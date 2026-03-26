#!/usr/bin/env bash
set -e

echo "→ Ejecutando migraciones..."
php artisan migrate --force

echo "→ Ejecutando seeders..."
php artisan db:seed --force

echo "→ Enlazando storage..."
php artisan storage:link 2>/dev/null || true

echo "→ Cacheando rutas y vistas..."
php artisan route:cache
php artisan view:cache

echo "→ Iniciando servidor en puerto ${PORT:-8000}..."
php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
