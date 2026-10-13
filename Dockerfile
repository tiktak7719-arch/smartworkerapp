# Build the Vite assets separately so the production image does not contain Node.js.
FROM node:22-bookworm-slim AS frontend

WORKDIR /app

COPY package.json .npmrc ./
RUN npm install

COPY . .
RUN npm run build

# Install PHP dependencies without development tooling.
FROM composer:2.8 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# FrankenPHP provides a production PHP server and Caddy web server in one image.
FROM dunglas/frankenphp:1-php8.5-bookworm

WORKDIR /app

RUN install-php-extensions pdo_pgsql \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build
COPY docker/start-container /usr/local/bin/start-container

RUN chmod +x /usr/local/bin/start-container \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

EXPOSE 10000

CMD ["/usr/local/bin/start-container"]
