# Nivel 11 — GenericWrite y Kerberoasting dirigido.
# pasante tiene GenericWrite sobre svc_web: le pone un SPN, pide el ticket de
# servicio, lo crackea y lee el Flag con esa cuenta.
starter_user
user_add svc_web 'estrella' --description='Cuenta de servicio del portal web'
ace svc_web "$GENERIC_WRITE" pasante
flag_share svc_web
