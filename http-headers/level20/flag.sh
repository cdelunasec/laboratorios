#!/bin/sh
# Coloca el flag de este nivel (lab local controlado). $1 = flag.
set -e
# Nivel de RCE: nombre aleatorio, solo se encuentra listando el sistema.
if ! ls /flag_*.txt >/dev/null 2>&1; then
    printf '%s\n' "$1" > "/flag_$(head -c 8 /dev/urandom | od -An -tx1 | tr -d ' \n').txt"
fi
chmod 444 /flag_*.txt
