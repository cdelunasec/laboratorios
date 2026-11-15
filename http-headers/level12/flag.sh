#!/bin/sh
# Coloca el flag de este nivel (lab local controlado). $1 = flag.
set -e
# Nivel de lectura: el flag va en la última línea de un informe de ~3 MB que
# solo se entrega por rangos de bytes.
mkdir -p /var/lib/labapp
F=/var/lib/labapp/informe-anual.txt
seq 1 60000 | sed 's/.*/Línea &: sin novedades en el registro de préstamos y ahorros./' > "$F"
printf 'Código de la asamblea: %s\n' "$1" >> "$F"
chown root:www-data /var/lib/labapp "$F"
chmod 750 /var/lib/labapp
chmod 440 "$F"
