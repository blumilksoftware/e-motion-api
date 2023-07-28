#!/usr/bin/env sh

echo "Git fetch newest tag"
cd /var/storage/www/ESCOOTERS/
git fetch
TAG="$(git describe --tags --abbrev=0 origin/main)"
FILE="docker-compose.staging.yml"

echo "Git checkout tag: " $TAG
git checkout tags/$TAG -f

echo "Execute composer and install packages"
docker-compose -f $FILE run --rm -u "$(id -u):$(id -g)" php composer install --optimize-autoloader

echo "Install npm assets"
docker-compose -f $FILE exec -T node npm install

echo "Build npm assets"
docker-compose -f $FILE exec -T node npm run build

echo "Run migrations and create caches"
docker-compose -f $FILE run --rm -u "$(id -u):$(id -g)" php php artisan migrate --force &&
    docker-compose -f $FILE run --rm -u "$(id -u):$(id -g)" php php artisan config:cache &&
    docker-compose -f $FILE run --rm -u "$(id -u):$(id -g)" php php artisan route:cache &&
    docker-compose -f $FILE run --rm -u "$(id -u):$(id -g)" php php artisan view:cache
