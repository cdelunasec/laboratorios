# Nivel 14 — gMSA.
# pasante puede leer la contraseña administrada (msDS-ManagedPassword) de la
# gMSA svc_gmsa. Con ella autenticas como svc_gmsa$ y lees el Flag.
starter_user
gmsa_add svc_gmsa 'svc-gmsa.quisqueya.lab' pasante
flag_share 'svc_gmsa$'
