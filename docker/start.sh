#!/bin/bash

# Migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Cache
php bin/console cache:warmup --env=prod

# Démarrage PHP-FPM en arrière-plan
php-fpm -D

# Démarrage Nginx au premier plan
nginx -g "daemon off;"
