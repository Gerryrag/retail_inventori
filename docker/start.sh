#!/bin/sh
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

# Support Railway's PORT environment variable
PORT=${PORT:-80}

# Generate nginx config with dynamic port
cat > /tmp/nginx.conf << EOF
server {
    listen $PORT;
    root /var/www/html/public;
    index index.php;

    client_max_body_size 50M;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

cp /tmp/nginx.conf /etc/nginx/http.d/default.conf

# Run migrations on startup (for Railway)
php /var/www/html/artisan migrate --force 2>/dev/null || true

php-fpm -D
nginx -g "daemon off;"
