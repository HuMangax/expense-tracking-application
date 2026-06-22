# syntax=docker/dockerfile:1

# ----------------------------------------------------------------------------
# Stage 1 — PHP dependencies (vendor/)
# ----------------------------------------------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev --no-scripts --no-interaction --prefer-dist \
        --ignore-platform-reqs --optimize-autoloader

# ----------------------------------------------------------------------------
# Stage 2 — Front-end assets (needs vendor/ because app.css imports Flux's CSS)
# ----------------------------------------------------------------------------
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
COPY resources ./resources
COPY public ./public
COPY --from=vendor /app/vendor ./vendor
RUN npm ci && npm run build

# ----------------------------------------------------------------------------
# Stage 3 — Runtime (FrankenPHP serves Laravel's public/ directory)
# ----------------------------------------------------------------------------
FROM dunglas/frankenphp:1-php8.4
RUN install-php-extensions pdo_pgsql intl zip bcmath opcache

WORKDIR /app

# Composer is needed to regenerate the optimized autoloader with the full app.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Application source, then the artifacts produced by the earlier stages.
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --no-dev --optimize --no-interaction \
        && chmod -R 775 storage bootstrap/cache

COPY docker/Caddyfile /etc/frankenphp/Caddyfile
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
