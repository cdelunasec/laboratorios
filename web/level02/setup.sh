# Datos de la mesa de ayuda fuera del webroot: tickets y bitácora de accesos.
mkdir -p /var/www/soporte/tickets /var/www/soporte/logs
touch /var/www/soporte/logs/acceso.log
chown -R www-data:www-data /var/www/soporte
# Un par de tickets de ejemplo.
printf 'Ticket #0001\nFecha: 2026-10-02 09:14\nCliente: Ferretería Hermanos Peña\nAsunto: La impresora no imprime\nEstado: cerrado\n\nDesde ayer la HP de caja no saca nada, se queda en cola.\n' > /var/www/soporte/tickets/0001.txt
printf 'Ticket #0002\nFecha: 2026-10-05 15:40\nCliente: Colmado La Esquina\nAsunto: WiFi lento\nEstado: abierto\n\nEl internet se cae cada rato por la tarde.\n' > /var/www/soporte/tickets/0002.txt
chown -R www-data:www-data /var/www/soporte
