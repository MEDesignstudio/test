# ==========================================================================
# GemeinsamKI – Docker-Image (PHP 8.3 + Apache)
# Start:  docker compose up -d --build
# ==========================================================================

# vendor/ (PHPMailer) liegt im Repository – kein Composer-Download beim Build nötig.
# Update: composer update auf dem Entwicklungsrechner, dann committen.

FROM php:8.3-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html \
    TZ=Europe/Berlin

RUN set -eux; \
    docker-php-ext-install -j"$(nproc)" opcache; \
    a2enmod rewrite headers expires deflate remoteip mime; \
    sed -ri 's#AllowOverride None#AllowOverride All#g' /etc/apache2/apache2.conf; \
    ln -sf /usr/share/zoneinfo/Europe/Berlin /etc/localtime

COPY docker/apache.conf /etc/apache2/conf-available/zz-gemeinsamki.conf
COPY docker/php.ini "$PHP_INI_DIR/conf.d/zz-gemeinsamki.ini"
COPY docker/entrypoint.sh /usr/local/bin/gemeinsamki-entrypoint
RUN a2enconf zz-gemeinsamki \
    && sed -ri 's#^(\s*CustomLog .*)$#\1 env=!dontlog#' /etc/apache2/sites-available/000-default.conf \
    && chmod +x /usr/local/bin/gemeinsamki-entrypoint

WORKDIR /var/www/html
COPY --chown=www-data:www-data . /var/www/html

# Standarddaten sichern – werden beim Start in ein leeres data/-Volume kopiert
RUN set -eux; \
    rm -rf docker Dockerfile docker-compose.yml .dockerignore .env .env.example; \
    mkdir -p /opt/gemeinsamki; \
    cp -a data /opt/gemeinsamki/data-default

EXPOSE 80
VOLUME ["/var/www/html/data"]

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD php -r 'exit(@file_get_contents("http://127.0.0.1/robots.txt") === false ? 1 : 0);'

ENTRYPOINT ["gemeinsamki-entrypoint"]
CMD ["apache2-foreground"]
