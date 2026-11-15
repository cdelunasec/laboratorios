#!/bin/sh
# Arranca el servicio interno del nivel (lab local controlado).
# Uso: start-interno <ip:puerto>
# Corre como el usuario `interno` (no como www-data) y escucha solo donde se le
# diga, normalmente 127.0.0.1. Usa /opt/interno/router.php como enrutador.
set -e
su -s /bin/sh interno -c "PHP_CLI_SERVER_WORKERS=4 nohup php -S '$1' -t /opt/interno /opt/interno/router.php >/dev/null 2>&1 &"
# Espera a que el puerto responda antes de seguir.
i=0
while [ $i -lt 50 ]; do
    if php -r '$p = explode(":", $argv[1]); $s = @fsockopen($p[0], (int) $p[1], $e, $m, 0.2); exit($s ? 0 : 1);' "$1"; then
        exit 0
    fi
    i=$((i + 1))
    sleep 0.1
done
echo "[!] El servicio interno no arrancó en $1" >&2
