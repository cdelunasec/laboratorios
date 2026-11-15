#!/bin/sh
# Coloca el flag de este nivel (lab local controlado). $1 = flag.
set -e
# Nivel de lectura: la app entrega el flag cuando burlas el control.
printf '%s\n' "$1" > /flag.txt
chmod 444 /flag.txt

# Cuentas: demo/demo y un admin con contraseña aleatoria que nadie conoce.
mkdir -p /var/lib/labapp
if [ "$LAB_FIRST_BOOT" = 1 ] || [ ! -f /var/lib/labapp/datos.json ]; then
    ADMIN_PASS="$(head -c 18 /dev/urandom | od -An -tx1 | tr -d ' \n')"
    ADMIN_PASS="$ADMIN_PASS" php -r '
        $d = ["usuarios" => [
            "admin" => password_hash(getenv("ADMIN_PASS"), PASSWORD_DEFAULT),
            "demo"  => password_hash("demo", PASSWORD_DEFAULT),
        ], "tokens" => new stdClass()];
        file_put_contents("/var/lib/labapp/datos.json", json_encode($d, JSON_PRETTY_PRINT));
    '
    unset ADMIN_PASS
fi
chown -R www-data:www-data /var/lib/labapp
chmod 700 /var/lib/labapp
chmod 600 /var/lib/labapp/datos.json
