# Nivel 5 — Kerberoasting.
# Entras como pasante y hay una cuenta de servicio con SPN y clave débil:
# pides el ticket de servicio, lo crackeas y lees el Flag.
starter_user
user_add svc_sql 'estrella' --description='Cuenta de servicio del servidor SQL'
spn_add svc_sql 'MSSQLSvc/sql01.quisqueya.lab:1433'
flag_share svc_sql
