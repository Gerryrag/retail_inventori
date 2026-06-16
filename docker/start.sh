#!/bin/sh
set -e

echo "Starting Laravel application..."

# Setup permissions
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

# Support Railway's PORT environment variable
PORT=${PORT:-80}
echo "Application will listen on port: $PORT"

# Create nginx config directory if not exists
mkdir -p /etc/nginx/http.d

# Generate nginx config with dynamic port
cat > /etc/nginx/http.d/default.conf << EOF
server {
    listen $PORT default_server;
    listen [::]:$PORT default_server;
    server_name _;
    
    root /var/www/html/public;
    index index.php index.html index.htm;

    client_max_body_size 50M;

    # Logs for debugging
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log warn;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        fastcgi_param SCRIPT_NAME \$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
    }

    location ~ /\.ht {
        deny all;
    }

    location ~ /\.env {
        deny all;
    }
}
EOF

echo "Nginx config created successfully"

# Test nginx config
if ! nginx -t 2>&1; then
    echo "Nginx config test failed!"
    exit 1
fi

echo "Nginx config test passed"

# Run migrations (optional, might fail if DB not ready)
echo "Running migrations..."
php /var/www/html/artisan migrate --force 2>&1 || echo "Migrations skipped (DB might not be ready yet)"

# Create necessary log directories
mkdir -p /var/log/nginx
mkdir -p /var/log/php-fpm

echo "Starting PHP-FPM..."
php-fpm -D

echo "Starting Nginx..."
nginx -g "daemon off;"
