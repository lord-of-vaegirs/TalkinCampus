#!/bin/sh
set -eu

DATADIR=/var/lib/mysql
RUNDIR=/run/mysqld
SOCKET="$RUNDIR/mysqld.sock"
INIT_MARKER="$DATADIR/.talkincampus_initialized"

mkdir -p "$DATADIR" "$RUNDIR"
chown -R mysql:mysql "$DATADIR" "$RUNDIR"

if [ ! -d "$DATADIR/mysql" ]; then
    mariadb-install-db \
        --user=mysql \
        --datadir="$DATADIR" \
        --auth-root-authentication-method=normal \
        --skip-test-db >/dev/null
fi

mariadbd-safe \
    --datadir="$DATADIR" \
    --socket="$SOCKET" \
    --bind-address=127.0.0.1 &
MYSQL_PID=$!

for _ in 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24 25 26 27 28 29 30 31 32 33 34 35 36 37 38 39 40 41 42 43 44 45 46 47 48 49 50 51 52 53 54 55 56 57 58 59 60; do
    if mysqladmin --protocol=socket --socket="$SOCKET" ping --silent; then
        break
    fi
    sleep 1
done

if ! mysqladmin --protocol=socket --socket="$SOCKET" ping --silent; then
    echo "MariaDB failed to start" >&2
    exit 1
fi

if [ ! -f "$INIT_MARKER" ]; then
    mysql --protocol=socket --socket="$SOCKET" < /docker-entrypoint-initdb.d/schema.sql
    mysql --protocol=socket --socket="$SOCKET" < /docker-entrypoint-initdb.d/seed.sql
    touch "$INIT_MARKER"
fi

apache2-foreground &
APACHE_PID=$!

stop_services() {
    apache2ctl -k graceful-stop >/dev/null 2>&1 || true
    mysqladmin --protocol=socket --socket="$SOCKET" shutdown >/dev/null 2>&1 || true
    wait "$APACHE_PID" >/dev/null 2>&1 || true
    wait "$MYSQL_PID" >/dev/null 2>&1 || true
}

trap stop_services INT TERM
wait "$APACHE_PID"
stop_services
