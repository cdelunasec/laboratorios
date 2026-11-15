#!/bin/sh
# Coloca el flag de este nivel (lab local controlado). $1 = flag.
set -e
# El flag vive dentro del código PHP de config.php: si lo incluyes se ejecuta y no se ve.
cat > /var/www/html/config.php <<EOF
<?php
// Configuración interna. NO PUBLICAR.
\$site_name = 'Clínica Dental Sonrisa';
\$db_host = 'localhost';
\$api_secret = '$1';
EOF
chmod 444 /var/www/html/config.php
