# Nivel 13 — WriteDACL sobre un grupo.
# pasante tiene WriteDACL sobre el grupo Administradores: se otorga permiso para
# agregarse (o cambia la DACL), se agrega al grupo y lee el Flag.
starter_user
group_add Administradores
ace Administradores "$WRITE_DACL" pasante
flag_share '@"QUISQUEYA\Administradores"'
