FROM php:8.2-apache

# Panel local de validación de flags. Solo escucha en 127.0.0.1.
# Nunca conoce los flags: compara el SHA-256 del que pegas con el que dejó cada nivel.

COPY http-headers/common/assets/style.css /var/www/html/assets/style.css
COPY panel/src/ /var/www/html/
COPY panel/entrypoint.sh /usr/local/bin/panel-entrypoint

RUN sed -i 's/\r$//' /usr/local/bin/panel-entrypoint \
    && chmod +x /usr/local/bin/panel-entrypoint \
    && chown -R root:root /var/www/html \
    && echo 'expose_php = Off' > /usr/local/etc/php/conf.d/00-panel.ini

ENTRYPOINT ["panel-entrypoint"]
CMD ["apache2-foreground"]
