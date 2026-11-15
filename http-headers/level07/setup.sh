# Dos vhosts: el público en /var/www/html y el interno en /var/www/intranet.
set -e
a2dissite 000-default >/dev/null
mv /var/www/html/_intranet /var/www/intranet
cp -r /var/www/html/assets /var/www/intranet/assets
chown -R root:root /var/www/intranet
