#!/bin/sh
set -e

# On Render, APP_URL defaults to the service's public URL if you didn't set one.
: "${APP_URL:=$RENDER_EXTERNAL_URL}"
export APP_URL

# Apply any pending migrations (no-op when the schema is already current).
php artisan migrate --force

# Cache config, events, routes, and compiled views for performance.
# (Routes are now controller/component based, so route caching is safe.)
php artisan optimize

# Hand off to the web server (replaces PID 1 so signals work correctly).
exec frankenphp run --config /etc/frankenphp/Caddyfile
