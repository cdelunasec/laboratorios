# Nivel 9 — ForceChangePassword.
# pasante tiene el derecho User-Force-Change-Password sobre jdelacruz. Le
# cambias la contraseña (bloodyAD set password) y lees el Flag con su cuenta.
starter_user
user_add jdelacruz "$(rand_pass)" --given-name='Joaquín' --surname='De la Cruz' --department='Dirección'
ace jdelacruz "$FORCE_CHANGE_PWD" pasante
flag_share jdelacruz
