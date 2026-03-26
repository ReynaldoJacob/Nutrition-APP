#!/usr/bin/env bash
set -e

echo "======= DEBUG VARIABLES ======="
echo "DB_HOST:     ${DB_HOST}"
echo "DB_PORT:     ${DB_PORT}"
echo "DB_DATABASE: ${DB_DATABASE}"
echo "DB_USERNAME: ${DB_USERNAME}"
echo "DB_PASSWORD set: $([ -n "$DB_PASSWORD" ] && echo 'SI' || echo 'NO')"
echo "APP_KEY set: $([ -n "$APP_KEY" ] && echo 'SI' || echo 'NO')"
echo "==============================="

echo "→ Esperando que MySQL esté listo..."
until php -r "
\$conn = @new mysqli('${DB_HOST}', '${DB_USERNAME}', '${DB_PASSWORD}', '${DB_DATABASE}', ${DB_PORT:-3306});
if (\$conn->connect_error) { exit(1); }
echo 'Conectado OK';
"; do
  echo "   MySQL no disponible, reintentando en 3s..."
  sleep 3
done

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
