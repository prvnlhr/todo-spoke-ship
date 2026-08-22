#!/bin/sh
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
  echo "Installing Composer dependencies..."
  composer install --no-interaction --prefer-dist --no-scripts
  composer dump-autoload --optimize
fi

mkdir -p \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  storage/app/public \
  storage/app/private \
  bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

echo "Waiting for MySQL at ${DB_HOST:-mysql}..."
until php -r "
try {
  new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s', getenv('DB_HOST') ?: 'mysql', getenv('DB_PORT') ?: '3306', getenv('DB_DATABASE') ?: 'todo'),
    getenv('DB_USERNAME') ?: 'todo',
    getenv('DB_PASSWORD') ?: 'secret'
  );
  exit(0);
} catch (Throwable \$e) {
  exit(1);
}
"; do
  sleep 2
done
echo "MySQL is ready."

if [ -z "$APP_KEY" ]; then
  echo "ERROR: APP_KEY is not set."
  exit 1
fi

php artisan migrate --force

if [ "${SEED_ON_BOOT:-false}" = "true" ] && [ "${APP_ROLE:-spoke}" = "hub" ]; then
  php artisan db:seed --force
fi

exec "$@"
