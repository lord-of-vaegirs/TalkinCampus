FROM php:8.2-apache

ENV DB_HOST=127.0.0.1 \
    DB_NAME=talkincampus \
    DB_USER=admin \
    DB_PASS=admin

RUN apt-get update \
    && apt-get install -y --no-install-recommends mariadb-server mariadb-client \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_mysql

COPY backend /var/www/html/backend
COPY frontend /var/www/html/frontend
COPY index.html /var/www/html/index.html
COPY database /docker-entrypoint-initdb.d

EXPOSE 80

CMD ["sh", "-c", "set -eu; DATADIR=/var/lib/mysql; RUNDIR=/run/mysqld; SOCKET=$RUNDIR/mysqld.sock; INIT_MARKER=$DATADIR/.talkincampus_initialized; mkdir -p $DATADIR $RUNDIR; chown -R mysql:mysql $DATADIR $RUNDIR; if [ ! -d $DATADIR/mysql ]; then mariadb-install-db --user=mysql --datadir=$DATADIR --auth-root-authentication-method=normal --skip-test-db >/dev/null; fi; mariadbd-safe --datadir=$DATADIR --socket=$SOCKET --bind-address=127.0.0.1 & MYSQL_PID=$!; for i in $(seq 1 60); do if mysqladmin --protocol=socket --socket=$SOCKET ping --silent; then break; fi; sleep 1; done; if ! mysqladmin --protocol=socket --socket=$SOCKET ping --silent; then echo 'MariaDB failed to start' >&2; exit 1; fi; if [ ! -f $INIT_MARKER ]; then mysql --protocol=socket --socket=$SOCKET < /docker-entrypoint-initdb.d/schema.sql; mysql --protocol=socket --socket=$SOCKET < /docker-entrypoint-initdb.d/seed.sql; touch $INIT_MARKER; fi; stop_services() { apache2ctl -k graceful-stop >/dev/null 2>&1 || true; mysqladmin --protocol=socket --socket=$SOCKET shutdown >/dev/null 2>&1 || true; wait $MYSQL_PID >/dev/null 2>&1 || true; }; trap stop_services INT TERM; apache2-foreground"]
