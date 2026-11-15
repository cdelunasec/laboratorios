# Dos vhosts: el público en /var/www/html y staging en /var/www/staging.
set -e
a2dissite 000-default >/dev/null
mv /var/www/html/_staging /var/www/staging
cp -r /var/www/html/assets /var/www/staging/assets
chown -R root:root /var/www/staging
