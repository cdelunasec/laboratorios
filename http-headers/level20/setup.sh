# Dos vhosts: estado público en /var/www/html y la consola en /var/www/ops.
set -e
a2dissite 000-default >/dev/null
mv /var/www/html/_ops /var/www/ops
cp -r /var/www/html/assets /var/www/ops/assets
chown -R root:root /var/www/ops
