#!/bin/sh
# Coloca el flag de este nivel (lab local controlado). $1 = flag.
set -e
# El flag lo imprime un panel viejo que quedó fuera del webroot.
mkdir -p /var/www/secret
printf '<?php\n// Panel interno viejo. No se usa, pero nadie lo borró.\necho "Panel interno - flag: %s";\n' "$1" > /var/www/secret/panel.php
chmod 444 /var/www/secret/panel.php
