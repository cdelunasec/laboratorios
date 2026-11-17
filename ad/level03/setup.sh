# Nivel 3 — password spraying.
# El directorio de empleados se enumera por LDAP anónimo y la política de
# claves es débil (sin complejidad ni bloqueo): uno de los empleados usa una
# contraseña común. Enumeras usuarios y rocías la contraseña hasta dar con él.
anon_ldap
user_add jsantana 'estrella' --given-name='José' --surname='Santana' --department='Ventas'
flag_share jsantana
