#!/bin/sh
# Coloca el flag de este nivel (lab local controlado). $1 = flag.
set -e
# Nivel de lectura: la app entrega el flag cuando burlas el control.
printf '%s\n' "$1" > /flag.txt
chmod 444 /flag.txt
