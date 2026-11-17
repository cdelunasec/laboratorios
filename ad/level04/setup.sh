# Nivel 4 — AS-REP roasting.
# Entras como pasante y encuentras una cuenta con DONT_REQ_PREAUTH: pides su
# AS-REP sin preautenticación, crackeas la clave y lees el Flag.
starter_user
user_add mfernandez 'mariposa' --given-name='Miguel' --surname='Fernández' --department='Finanzas'
uac_add mfernandez 4194304
flag_share mfernandez
