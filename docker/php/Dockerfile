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

COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

WORKDIR /var/www/html

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80
CMD ["/start.sh"]