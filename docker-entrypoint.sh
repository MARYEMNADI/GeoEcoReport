#!/bin/sh

set -e

echo "⏳ En attente de la base de données ($DB_HOST:$DB_PORT)..."

until php -r "new PDO('mysql:host=$DB_HOST;port=$DB_PORT', getenv('DB_USERNAME'), getenv('DB_PASSWORD'));" 2>/dev/null; do
    sleep 1
done

echo "✅ Base de données prête."


if [ ! -d "vendor" ]; then

    echo "📦 Installation des dépendances Composer..."

    composer install \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader

fi


if [ ! -f ".env" ]; then

    echo "📄 Copie de .env.example vers .env..."

    cp .env.example .env

fi


if ! grep -q "^APP_KEY=base64" .env 2>/dev/null; then

    echo "🔑 Génération de la clé d'application..."

    php artisan key:generate --force

fi


echo "🗄️ Exécution des migrations..."

php artisan migrate --force


chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true


echo "🚀 Démarrage du serveur..."

exec "$@"