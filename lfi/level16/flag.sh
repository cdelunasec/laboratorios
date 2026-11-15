#!/bin/sh
# Coloca el flag de este nivel (lab local controlado). $1 = flag.
set -e
# Nivel de RCE: nombre aleatorio, solo se encuentra listando / con ejecución de comandos.
if ! ls /flag_*.txt >/dev/null 2>&1; then
    rand() { head -c "$1" /dev/urandom | od -An -tx1 | tr -d ' \n'; }
    printf '%s\n' "$1" > "/flag_$(rand 8).txt"
    chmod 444 /flag_*.txt
fi

mkdir -p /var/lib/php/sessions
chown www-data:www-data /var/lib/php/sessions
chmod 1733 /var/lib/php/sessions
