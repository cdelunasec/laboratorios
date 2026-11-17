# Nivel 1 — sesión nula y LDAP anónimo.
# El LDAP permite lectura anónima y en la descripción de una cuenta de respaldo
# quedó la contraseña. Enumeras por LDAP, encuentras las credenciales y lees el
# share Flag con esa cuenta.
anon_ldap
user_add respaldo 'mariposa' --description='Cuenta de respaldo. Contraseña: mariposa'
flag_share respaldo
