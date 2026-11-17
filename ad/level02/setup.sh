# Nivel 2 — share de invitado.
# El share Publico acepta invitados: una sesión nula (-N) entra y en las notas
# quedó la cuenta de impresión con su contraseña.
guest_on
user_add svc_impresoras 'corazon' --description='Cuenta de servicio de impresoras'
share Publico /srv/publico 'guest ok = yes'
printf 'Notas de IT: la cuenta de impresion es svc_impresoras / corazon\n' > /srv/publico/notas.txt
flag_share svc_impresoras
