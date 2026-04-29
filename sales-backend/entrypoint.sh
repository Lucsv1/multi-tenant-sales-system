#!/bin/sh
set -e

REQUIRED_VARS="DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD"

for VAR in $REQUIRED_VARS; do
    eval VALUE=\$$VAR
    if [ -z "$VALUE" ]; then
        echo "ERROR: variavel $VAR nao definida. Pulando migrate e seed."
        exec php-fpm
    fi
done

echo "Variaveis de ambiente OK. Rodando migrate e seed..."
php artisan migrate --force
php artisan db:seed --force

exec php-fpm
