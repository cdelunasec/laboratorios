# Nivel 10 — GenericAll sobre un grupo.
# pasante tiene GenericAll sobre el grupo Tecnicos: se agrega a sí mismo y, como
# el share Flag deja entrar a los miembros del grupo, lo lee.
starter_user
group_add Tecnicos
user_add lrosa "$(rand_pass)" --given-name='Luis' --surname='Rosa' --department='Soporte'
ace Tecnicos "$GENERIC_ALL" pasante
flag_share '@"QUISQUEYA\Tecnicos"'
