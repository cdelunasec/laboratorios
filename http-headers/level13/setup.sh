# La librería de cuentas va fuera del webroot.
set -e
mv /var/www/html/_lib/cuentas.php /var/www/lab/cuentas.php
rmdir /var/www/html/_lib
