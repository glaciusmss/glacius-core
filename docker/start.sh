#!/usr/bin/env bash

set -e

role=${CONTAINER_ROLE:-app}

chmod 777 -R storage/

if [ "$role" = "app" ]; then

    ln -sf /etc/supervisor/conf.d-available/app.conf /etc/supervisor/conf.d/app.conf
    ln -sf /etc/supervisor/conf.d-available/horizon.conf /etc/supervisor/conf.d/horizon.conf

    exec supervisord -c /etc/supervisor/supervisord.conf

elif [ "$role" = "websocket" ]; then

    rm .env && cp websocket.env .env

    php artisan config:cache
    php artisan route:cache

    ln -sf /etc/supervisor/conf.d-available/websocket.conf /etc/supervisor/conf.d/websocket.conf

    exec supervisord -c /etc/supervisor/supervisord.conf

elif [ "$role" = "scheduler" ]; then

    php artisan config:cache
    php artisan route:cache

    while [ true ]
    do
      php /var/www/html/artisan schedule:run --verbose --no-interaction &
      sleep 60
    done

else
    echo "Could not match the container role \"$role\""
    exit 1
fi
