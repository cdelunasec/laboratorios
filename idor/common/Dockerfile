FROM php:8.2-apache

# LAB LOCAL INTENCIONALMENTE VULNERABLE (IDOR).
# Entorno controlado de práctica: se publica solo en 127.0.0.1. No exponer a ninguna red.
# Sin apt-get: pdo_sqlite ya viene en la imagen de PHP.

COPY common/entrypoint.sh /usr/local/bin/lab-entrypoint
COPY common/apache.conf /etc/apache2/conf-available/docker-php.conf
COPY common/lab.php /var/www/lab/lab.php

ARG LEVEL
ENV LAB_ID=idor-${LEVEL} LAB_LEVEL=${LEVEL}

COPY common/assets/ /var/www/html/assets/
COPY common/web/ /var/www/html/
COPY level${LEVEL}/nivel.php /var/www/lab/nivel.php
COPY level${LEVEL}/seed.php /var/www/lab/seed.php
COPY level${LEVEL}/src/ /var/www/html/

RUN sed -i 's/\r$//' /usr/local/bin/lab-entrypoint \
    && chmod +x /usr/local/bin/lab-entrypoint \
    && chown -R root:root /var/www/html /var/www/lab \
    && find /var/www/html /var/www/lab -type d -exec chmod 755 {} + \
    && find /var/www/html /var/www/lab -type f -exec chmod 644 {} + \
    && chmod 700 /var/www/lab/seed.php

ENTRYPOINT ["lab-entrypoint"]
CMD ["apache2-foreground"]
