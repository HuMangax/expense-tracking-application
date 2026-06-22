#!/bin/sh
set -e

# On Render, APP_URL defaults to the service's public URL if you didn't set one.
: "${APP_URL:=$RENDER_EXTERNAL_URL}"
export APP_URL

# Apply any pending migrations (no-op when the schema is already current).
php artisan migrate --force

# Cache config & compiled views for performance.
# NOTE: routes are intentionally NOT cached — the app uses closure-based routes.
php artisan config:cache
php artisan view:cache

# Hand off to the web server (replaces PID 1 so signals work correctly).
exec frankenphp run --config /etc/frankenphp/Caddyfile
