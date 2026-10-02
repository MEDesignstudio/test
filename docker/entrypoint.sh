#!/bin/sh
# GemeinsamKI – Container-Start: Benutzer-ID anpassen, data/ vorbereiten, Apache starten
set -e

APP_ROOT=/var/www/html
DATA_DIR="$APP_ROOT/data"
DEFAULTS=/opt/gemeinsamki/data-default

# Optional: www-data auf die UID/GID des Host-Benutzers setzen (für ./data als Bind-Mount)
if [ "${APP_UID:-}" = "0" ] || [ "${APP_GID:-}" = "0" ]; then
    echo "GemeinsamKI: APP_UID/APP_GID 0 (root) ist nicht erlaubt – www-data bleibt bei UID $(id -u www-data)." >&2
elif [ -n "${APP_UID:-}" ] && [ "$APP_UID" != "$(id -u www-data)" ]; then
    groupmod -o -g "${APP_GID:-$APP_UID}" www-data
    usermod -o -u "$APP_UID" -g "${APP_GID:-$APP_UID}" www-data
    chown -R www-data:www-data "$APP_ROOT"
fi

# Fehlende Standarddateien in data/ ergänzen (vorhandene werden nie überschrieben)
mkdir -p "$DATA_DIR/logs"
cp -an "$DEFAULTS/." "$DATA_DIR/" 2>/dev/null || true
chown -R www-data:www-data "$DATA_DIR"
chown www-data:www-data "$APP_ROOT"

# sitemap.xml beim Start neu erzeugen
su -s /bin/sh www-data -c "php $APP_ROOT/sitemap.php > /dev/null" || true

exec docker-php-entrypoint "$@"
