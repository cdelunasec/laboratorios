#!/bin/sh
# Coloca el flag de este nivel (lab local controlado). $1 = flag.
set -e
# Nivel de lectura: la app entrega el flag cuando burlas el control.
printf '%s\n' "$1" > /flag.txt
chmod 444 /flag.txt

# Clave HMAC de los tokens: aleatoria (no hace falta adivinarla).
mkdir -p /var/lib/labapp
if [ "$LAB_FIRST_BOOT" = 1 ] || [ ! -f /var/lib/labapp/jwt.key ]; then
    head -c 32 /dev/urandom | od -An -tx1 | tr -d ' \n' > /var/lib/labapp/jwt.key
fi
chown root:www-data /var/lib/labapp /var/lib/labapp/jwt.key
chmod 750 /var/lib/labapp
chmod 440 /var/lib/labapp/jwt.key
