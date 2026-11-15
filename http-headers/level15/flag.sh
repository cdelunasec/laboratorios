#!/bin/sh
# Coloca el flag de este nivel (lab local controlado). $1 = flag.
set -e
# Nivel de lectura: la app entrega el flag cuando burlas el control.
printf '%s\n' "$1" > /flag.txt
chmod 444 /flag.txt

# PIN aleatorio de 4 dígitos y contador de intentos por IP.
mkdir -p /var/lib/labapp/intentos
if [ "$LAB_FIRST_BOOT" = 1 ] || [ ! -f /var/lib/labapp/pin ]; then
    N=$(od -An -tu2 -N2 /dev/urandom | tr -d ' ')
    printf '%04d' $((N % 10000)) > /var/lib/labapp/pin
    rm -f /var/lib/labapp/intentos/*
fi
chown root:www-data /var/lib/labapp /var/lib/labapp/pin
chmod 750 /var/lib/labapp
chmod 440 /var/lib/labapp/pin
chown www-data:www-data /var/lib/labapp/intentos
chmod 700 /var/lib/labapp/intentos
