FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends mariadb-server mariadb-client \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_mysql

COPY backend /var/www/html/backend
COPY frontend /var/www/html/frontend
COPY database /docker-entrypoint-initdb.d
COPY docker/start-container.sh /usr/local/bin/start-container.sh

ENTRYPOINT ["sh", "/usr/local/bin/start-container.sh"]
