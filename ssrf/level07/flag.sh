#!/bin/sh
# Coloca el flag de este nivel (lab local controlado). $1 = flag.
set -e
# Solo el usuario `interno` puede leer el flag; lo sirve el servicio interno,
# que escucha únicamente en 127.0.0.1:8080.
printf '%s\n' "$1" > /var/lib/interno/flag
chown interno:interno /var/lib/interno/flag
chmod 400 /var/lib/interno/flag
start-interno 127.0.0.1:8080
