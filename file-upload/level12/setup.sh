mkdir -p /var/www/html/uploads/vacaciones /var/www/html/uploads/familia /var/www/html/uploads/mascotas
chown -R www-data:www-data /var/www/html/uploads
# El script de despliegue dejó la raíz web a nombre de www-data
chown www-data:www-data /var/www/html
