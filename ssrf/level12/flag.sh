#!/bin/sh
# Coloca el flag de este nivel (lab local controlado). $1 = flag.
set -e
printf '%s\n' "$1" > /var/lib/interno/flag
chown interno:interno /var/lib/interno/flag
chmod 400 /var/lib/interno/flag
# Servicio de metadatos: metadata.nube.local (-> 127.0.0.1) puerto 8775.
start-interno 127.0.0.1:8775
