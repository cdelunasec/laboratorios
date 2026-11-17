# Nivel 15 — cadena de varios saltos.
# Encadena derechos: pasante -> GenericAll sobre u1 -> toma u1; u1 -> GenericAll
# sobre u2 -> toma u2; u2 -> GenericAll sobre el grupo Final -> se agrega y lee
# el Flag.
starter_user
user_add u1 "$(rand_pass)"
user_add u2 "$(rand_pass)"
group_add Final
ace u1 "$GENERIC_ALL" pasante
ace u2 "$GENERIC_ALL" u1
ace Final "$GENERIC_ALL" u2
flag_share '@"QUISQUEYA\Final"'
