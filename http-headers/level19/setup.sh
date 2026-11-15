# Carpeta de caché escribible por la app y la copia de respaldo olvidada.
set -e
mkdir -p /var/www/html/cache
touch /var/www/html/cache/visitas.log
chown -R www-data:www-data /var/www/html/cache
chmod 755 /var/www/html/cache
cp /var/www/html/index.php /var/www/html/index.php.bak
chmod 644 /var/www/html/index.php.bak
