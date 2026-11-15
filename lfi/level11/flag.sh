#!/bin/sh
# Coloca el flag de este nivel (lab local controlado). $1 = flag.
set -e
# Nivel de lectura: el flag vive en una ruta conocida.
printf '%s\n' "$1" > /flag.txt
chmod 444 /flag.txt
