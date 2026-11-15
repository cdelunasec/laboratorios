#!/bin/sh
# Coloca el flag de este nivel (lab local controlado). $1 = flag.
set -e
# Este nivel no usa servicio interno: el flag está en /flag.txt y se lee con file://.
printf '%s\n' "$1" > /flag.txt
chmod 444 /flag.txt
