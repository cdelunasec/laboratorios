#!/bin/sh
# Coloca el flag de este nivel (lab local controlado). $1 = flag.
set -e
printf '%s\n' "$1" > /var/lib/interno/flag
chown interno:interno /var/lib/interno/flag
chmod 400 /var/lib/interno/flag

# El servicio interno escucha en un puerto aleatorio entre 8000 y 8999,
# elegido una sola vez por contenedor.
if [ "$LAB_FIRST_BOOT" = 1 ] || [ ! -f /var/lib/lab/puerto ]; then
    N=$(od -An -tu2 -N2 /dev/urandom | tr -d ' ')
    echo $((8000 + N % 1000)) > /var/lib/lab/puerto
fi
start-interno "127.0.0.1:$(cat /var/lib/lab/puerto)"
