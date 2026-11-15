#!/bin/sh
# El panel guarda el progreso en /flags/panel (montado desde ../.flags).
set -e
mkdir -p /flags/panel
chown www-data:www-data /flags/panel
exec "$@"
