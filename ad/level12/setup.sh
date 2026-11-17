# Nivel 12 — WriteOwner.
# pasante tiene WriteOwner sobre cmedina: se convierte en dueño del objeto y,
# como dueño, se concede derechos para cambiarle la contraseña y leer el Flag.
starter_user
user_add cmedina "$(rand_pass)" --given-name='Carmen' --surname='Medina' --department='Compras'
ace cmedina "$WRITE_OWNER" pasante
flag_share cmedina
