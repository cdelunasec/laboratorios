# Nivel 7 — script de NETLOGON.
# En el share NETLOGON hay un script de login con una contraseña en claro para
# mapear una unidad. Lo lees con pasante y usas esa cuenta para leer el Flag.
starter_user
user_add svc_inventario 'corazon' --description='Cuenta de servicio de inventario'
mkdir -p "$NETLOGON"
cat > "$NETLOGON/inventario.cmd" <<'EOF'
@echo off
rem Mapeo de la unidad de inventario (no borrar)
net use I: \\fileserver\inventario /user:QUISQUEYA\svc_inventario corazon /persistent:no
EOF
flag_share svc_inventario
