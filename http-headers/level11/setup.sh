# La librería JWT va fuera del webroot.
set -e
mv /var/www/html/_lib/jwt.php /var/www/lab/jwt.php
rmdir /var/www/html/_lib
