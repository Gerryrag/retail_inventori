FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    nginx \
    nodejs \
    npm \
    git \
    curl \
    unzip \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    postgresql-client \
    linux-headers

RUN docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    mbstring \
    zip \
    gd \
    opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy Laravel source code
COPY src/ .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy nginx config
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Copy start script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Install Node dependencies and build assets
RUN npm ci && npm run build

# Create necessary directories and set permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/views && \
    chmod -R 777 storage bootstrap/cache

EXPOSE 80
ENV PORT=80
CMD ["/start.sh"]