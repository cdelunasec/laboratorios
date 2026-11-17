# Nota que revela que svc_monitor es de confianza para delegar a cifs/dc01.
mkdir -p /opt
cat > /opt/notas.txt <<'EOF'
Monitorizacion: la cuenta svc_monitor esta marcada como "de confianza para
delegar" al servicio cifs de dc01. Revisar si ese permiso sigue haciendo falta
despues de la migracion del portal.
EOF
chmod 644 /opt/notas.txt
