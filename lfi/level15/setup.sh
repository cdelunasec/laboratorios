# La imagen oficial manda los logs a stdout. Aquí se escriben en archivo, como en un servidor normal.
rm -f /var/log/apache2/access.log /var/log/apache2/error.log /var/log/apache2/other_vhosts_access.log
touch /var/log/apache2/access.log /var/log/apache2/error.log
