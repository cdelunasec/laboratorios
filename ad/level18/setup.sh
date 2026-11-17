# Nivel 18 — Kerberoasting + delegación restringida.
# Foothold a srv01 (keytab de svc_backup) y una nota revela que la cuenta de
# monitoreo svc_monitor es "de confianza para delegar" a cifs/dc01. La
# kerberoasteas (SPN + clave débil) y, con S4U2Self/S4U2Proxy, te haces un
# ticket de servicio como Administrator y lees el Flag.
starter_user
user_add svc_backup "$(rand_pass)" --description='Cuenta de servicio de respaldos'
user_add svc_monitor 'mariposa' --description='Cuenta de servicio de monitoreo'
spn_add svc_monitor 'MSSQLSvc/monitor.quisqueya.lab:1433'
uac_add svc_monitor 16777216
attr_set svc_monitor msDS-AllowedToDelegateTo 'cifs/dc01.quisqueya.lab'
share Backups /srv/backups
flag_share Administrator
