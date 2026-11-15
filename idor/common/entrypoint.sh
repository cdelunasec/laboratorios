#!/bin/sh
# Lab local de IDOR (entorno controlado). Genera el flag una sola vez por
# contenedor, crea la base de datos del nivel con el flag escondido en los datos
# de otro usuario y deja el SHA-256 del flag en /lab-hashes para el panel local.
# El flag nunca va en variables de entorno ni en un archivo que lea www-data.
set -e

STATE=/var/lib/lab
mkdir -p "$STATE"
chmod 700 "$STATE"

if [ ! -f "$STATE/flag" ]; then
    rand() { head -c "$1" /dev/urandom | od -An -tx1 | tr -d ' \n'; }
    echo "cdelunasec{${LAB_ID}_$(rand 12)}" > "$STATE/flag"
    chmod 600 "$STATE/flag"
fi

if [ ! -f /var/lab/app.db ]; then
    mkdir -p /var/lab
    php /var/www/lab/seed.php
    chown -R www-data:www-data /var/lab
    chmod 700 /var/lab
fi

if [ -d /lab-hashes ]; then
    tr -d '\n' < "$STATE/flag" | sha256sum | cut -d' ' -f1 > "/lab-hashes/$LAB_LEVEL"
    chmod 644 "/lab-hashes/$LAB_LEVEL"
fi

exec "$@"
