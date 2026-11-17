# Nivel 20 — jefe final (foothold + Kerberoasting + GenericAll).
# Foothold a srv01 (keytab de svc_backup) y la nota revela que la cuenta de
# auditoría svc_auditoria es kerberoasteable. La crackeas y, como tiene
# GenericAll sobre el grupo Consejo, te agregas y lees el Flag.
starter_user
user_add svc_backup "$(rand_pass)" --description='Cuenta de servicio de respaldos'
user_add svc_auditoria 'mariposa' --description='Cuenta de servicio de auditoría'
spn_add svc_auditoria 'MSSQLSvc/auditoria.quisqueya.lab:1433'
group_add Consejo
ace Consejo "$GENERIC_ALL" svc_auditoria
share Backups /srv/backups
flag_share '@"QUISQUEYA\Consejo"'
