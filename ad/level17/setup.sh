# Nivel 17 — delegación restringida (constrained delegation).
# Entras a srv01 con el keytab de svc_backup, encuentras las credenciales de
# svc_web en la config del portal, y svc_web puede delegar a cifs/dc01: con
# S4U2Self/S4U2Proxy te haces un ticket de servicio como Administrator y lees
# el Flag en el DC.
starter_user
user_add svc_backup "$(rand_pass)" --description='Cuenta de servicio de respaldos'
user_add svc_web 'X7kQ2mP9vR4tW8zA' --description='Cuenta de servicio del portal web'
spn_add svc_web 'http/portal.quisqueya.lab'
uac_add svc_web 16777216
attr_set svc_web msDS-AllowedToDelegateTo 'cifs/dc01.quisqueya.lab'
share Backups /srv/backups
flag_share Administrator
