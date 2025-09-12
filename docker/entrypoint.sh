#!/usr/bin/env bash
set -e

# Configure Apache to listen on Render's PORT (default 80 locally)
APACHE_PORT="${PORT:-80}"
if [ -f /etc/apache2/ports.conf ]; then
  sed -ri "s/^Listen 80/Listen ${APACHE_PORT}/" /etc/apache2/ports.conf || true
fi
if [ -f /etc/apache2/sites-available/000-default.conf ]; then
  sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${APACHE_PORT}>/" /etc/apache2/sites-available/000-default.conf || true
  if ! grep -q "ServerName" /etc/apache2/sites-available/000-default.conf; then
    sed -i "2i\\    ServerName localhost" /etc/apache2/sites-available/000-default.conf || true
  fi
fi

# Ensure runtime caches are rebuilt with current env vars
if [ -f /var/www/html/artisan ]; then
  php /var/www/html/artisan config:clear || true
  php /var/www/html/artisan cache:clear || true
  php /var/www/html/artisan route:clear || true
  php /var/www/html/artisan view:clear || true

  php /var/www/html/artisan config:cache || true
  php /var/www/html/artisan route:cache || true

  if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php /var/www/html/artisan migrate --force || true
  fi
fi

exec apache2-foreground


