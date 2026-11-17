# Nivel 16 — foothold: keytab expuesto.
# La cuenta de respaldos svc_backup dejó su keytab "olvidado" en el share
# Backups. Lo descargas como pasante, pides un TGT con él y entras por SSH a
# srv01 con Kerberos (GSSAPI); el flag está en su home.
starter_user
user_add svc_backup "$(rand_pass)" --description='Cuenta de servicio de respaldos'
share Backups /srv/backups
