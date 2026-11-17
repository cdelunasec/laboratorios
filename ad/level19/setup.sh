# Nivel 19 — cadena completa (foothold + delegación + grupo).
# Encadenas: foothold a srv01 (keytab de svc_backup) -> credenciales de svc_web
# en su config -> svc_web delega a cifs/dc01 -> te haces un ticket como svc_sql,
# que es miembro del grupo Consejo, y el share Flag deja entrar a Consejo.
starter_user
user_add svc_backup "$(rand_pass)" --description='Cuenta de servicio de respaldos'
user_add svc_web 'X7kQ2mP9vR4tW8zA' --description='Cuenta de servicio del portal web'
spn_add svc_web 'http/portal.quisqueya.lab'
uac_add svc_web 16777216
attr_set svc_web msDS-AllowedToDelegateTo 'cifs/dc01.quisqueya.lab'
user_add svc_sql "$(rand_pass)" --description='Cuenta de servicio del servidor SQL'
spn_add svc_sql 'MSSQLSvc/sql01.quisqueya.lab:1433'
group_add Consejo
member_add Consejo svc_sql
share Backups /srv/backups
flag_share '@"QUISQUEYA\Consejo"'
