#!/bin/sh
# Genera el flag del nivel la primera vez que arranca el contenedor.
# Nombre y contenido aleatorios: solo se encuentra listando el sistema con RCE.
set -e

if ! ls /flag_*.txt >/dev/null 2>&1; then
    rand() { head -c "$1" /dev/urandom | od -An -tx1 | tr -d ' \n'; }
    FLAG_FILE="/flag_$(rand 8).txt"
    echo "cdelunasec{${LAB_ID}_$(rand 12)}" > "$FLAG_FILE"
    chmod 444 "$FLAG_FILE"
fi

exec "$@"
