#!/bin/sh
# Coloca el flag del nivel (lab local controlado). $1 = flag.
set -e
# El flag lo muestra /panel.php a quien tenga el rol de administrador.
printf '%s\n' "$1" > /flag.txt
chmod 440 /flag.txt
chown root:www-data /flag.txt

# Sesión del admin (la que usa el bot) y datos de la app: nuevos en cada
# contenedor, así un reset deja el nivel limpio.
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
