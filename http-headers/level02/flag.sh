#!/bin/sh
# Coloca el flag de este nivel (lab local controlado). $1 = flag.
set -e
# Nivel de lectura: la app entrega el flag cuando burlas el control.
printf '%s\n' "$1" > /flag.txt
chmod 444 /flag.txt

# Contraseña del portal: aleatoria y desconocida (no es parte del reto).
mkdir -p /var/lib/labapp
if [ "$LAB_FIRST_BOOT" = 1 ] || [ ! -f /var/lib/labapp/clave ]; then
    head -c 16 /dev/urandom | od -An -tx1 | tr -d ' \n' > /var/lib/labapp/clave
fi
chown root:www-data /var/lib/labapp /var/lib/labapp/clave
chmod 750 /var/lib/labapp
chmod 440 /var/lib/labapp/clave
