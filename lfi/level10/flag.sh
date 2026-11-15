#!/bin/sh
# Coloca el flag de este nivel (lab local controlado). $1 = flag.
set -e
# El flag está en un .txt que el LFI no alcanza (la app le pega .php).
# Solo lo muestra admin.php, y la contraseña está en config.php.
mkdir -p /opt/vault
printf '%s\n' "$1" > /opt/vault/flag.txt
chmod 444 /opt/vault/flag.txt
PASS="$(printf '%s' "$1" | sha1sum | cut -c1-14)"
cat > /var/www/html/config.php <<EOF
<?php
// Credenciales del sistema. NO PUBLICAR.
\$db_user = 'factura';
\$db_pass = 'f4ctur4-db';
\$admin_user = 'admin';
\$admin_pass = '$PASS';
EOF
chmod 444 /var/www/html/config.php
