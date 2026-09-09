#!/bin/sh
set -e

echo "Waiting for database..."

# Attendre que MySQL soit prêt
until php -r "new PDO(
    'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
    getenv('DB_USERNAME'),
    getenv('DB_PASSWORD')
);" 2>/dev/null; do
    sleep 2
done

echo "Database is ready."

# Générer APP_KEY si manquante
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force --no-interaction
fi

# Lien symbolique du storage
php artisan storage:link 2>/dev/null || true

# Migrations
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Seeders (optionnel)
echo "Running seeders..."
php artisan db:seed --force --no-interaction 2>/dev/null || true

echo "Starting Laravel development server..."
exec php artisan serve --host=0.0.0.0 --port=8000