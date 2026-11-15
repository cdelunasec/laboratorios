#!/bin/sh
# Lab local de cabeceras HTTP (entorno controlado). Genera el flag del nivel una
# sola vez por contenedor, lo coloca donde el nivel lo espera (flag.sh) y deja el
# hash SHA-256 en /lab-hashes para que el panel local (./lab.sh panel) pueda
# validarlo sin conocer el flag. El flag nunca va en variables de entorno.
set -e

STATE=/var/lib/lab
mkdir -p "$STATE"
chmod 700 "$STATE"

if [ ! -f "$STATE/flag" ]; then
    rand() { head -c "$1" /dev/urandom | od -An -tx1 | tr -d ' \n'; }
    echo "cdelunasec{${LAB_ID}_$(rand 12)}" > "$STATE/flag"
    chmod 600 "$STATE/flag"
    FIRST_BOOT=1
else
    FIRST_BOOT=0
fi

FLAG="$(cat "$STATE/flag")"
LAB_FIRST_BOOT=$FIRST_BOOT sh /opt/lab/flag.sh "$FLAG"

if [ -d /lab-hashes ]; then
    printf '%s' "$FLAG" | sha256sum | cut -d' ' -f1 > "/lab-hashes/$LAB_LEVEL"
    chmod 644 "/lab-hashes/$LAB_LEVEL"
fi

unset FLAG FIRST_BOOT
exec "$@"
