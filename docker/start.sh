#!/bin/sh
set -e

echo "=== Starting Laravel Application ==="

# Setup permissions
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

# Support Railway's PORT environment variable
PORT=${PORT:-80}
echo "[INFO] Application will listen on port: $PORT"

# Create necessary directories
mkdir -p /etc/nginx/http.d /var/log/nginx /var/log/php-fpm

# Generate nginx config with dynamic port
echo "[INFO] Generating Nginx configuration..."
cat > /etc/nginx/http.d/default.conf << 'EOF'
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name _;
    
    root /var/www/html/public;
    index index.php index.html index.htm;

    client_max_body_size 50M;

    # Logs
    access_log /var/log/nginx/access.log combined;
    error_log /var/log/nginx/error.log warn;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }

    location ~ /\.ht {
        deny all;
    }

    location ~ /\.env {
        deny all;
    }

    location /health {
        access_log off;
        return 200 "OK";
        add_header Content-Type text/plain;
    }
}
EOF

# Test nginx config
echo "[INFO] Testing Nginx configuration..."
if ! nginx -t 2>&1; then
    echo "[ERROR] Nginx config test failed!"
    exit 1
fi
echo "[INFO] Nginx config test passed"

# Clear caches
echo "[INFO] Clearing Laravel caches..."
php /var/www/html/artisan config:cache 2>&1 || true
php /var/www/html/artisan route:cache 2>&1 || true

# Run database migrations
echo "[INFO] Running database migrations..."
if php /var/www/html/artisan migrate --force 2>&1; then
    echo "[INFO] Migrations completed successfully"
else
    echo "[WARNING] Migrations failed or skipped (database might not be ready yet)"
fi

# Start PHP-FPM in background
echo "[INFO] Starting PHP-FPM..."
php-fpm -D

# Give PHP-FPM time to start
sleep 2

# Check if PHP-FPM is responding
echo "[INFO] Checking PHP-FPM status..."
if ! nc -z 127.0.0.1 9000 2>/dev/null; then
    echo "[WARNING] PHP-FPM might not be responsive, continuing anyway..."
fi

# Start Nginx in foreground
echo "[INFO] Starting Nginx..."
echo "=== Application Ready ==="
nginx -g "daemon off;"
