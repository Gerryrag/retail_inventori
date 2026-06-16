#!/bin/sh
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

php-fpm -D
nginx -g "daemon off;"
