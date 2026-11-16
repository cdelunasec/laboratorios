#!/bin/sh
# Coloca el flag del nivel (lab local controlado). $1 = flag.
set -e

# Todas las webs terminan en RCE: nombre aleatorio, solo se encuentra
# ejecutando comandos (o código) y listando el sistema de archivos.
if ! ls /flag_*.txt >/dev/null 2>&1; then
    printf '%s\n' "$1" > "/flag_$(head -c 8 /dev/urandom | od -An -tx1 | tr -d ' \n').txt"
fi
chmod 444 /flag_*.txt

# Datos de la app y sesión del admin (la que usa el bot en los niveles que lo
# necesitan): nuevos en cada contenedor, así un reset deja el nivel limpio.
D=/var/lib/labapp
if [ "$LAB_FIRST_BOOT" = 1 ] || [ ! -f "$D/admin_token" ]; then
    head -c 24 /dev/urandom | od -An -tx1 | tr -d ' \n' > "$D/admin_token"
    rm -rf "$D/datos" && mkdir -p "$D/datos"
fi
chown root:www-data "$D" "$D/admin_token"
chmod 750 "$D"
chmod 440 "$D/admin_token"
chown www-data:www-data "$D/datos"
chmod 700 "$D/datos"
