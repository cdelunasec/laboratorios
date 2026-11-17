# Pista que encuentra svc_backup tras entrar por SSH.
mkdir -p /opt
cat > /opt/notas.txt <<'EOF'
Auditoria: la cuenta svc_auditoria tiene un SPN y "manda" sobre el grupo Consejo
(permiso que nunca se revoco tras la auditoria). Revisar la vigencia de esa cuenta.
EOF
chmod 644 /opt/notas.txt
