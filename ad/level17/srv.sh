# El portal web que corre en srv01 guarda sus credenciales en su config
# (legible por cualquier usuario del dominio que entre por SSH).
mkdir -p /opt/webapp
cat > /opt/webapp/config.ini <<'EOF'
[web]
user = QUISQUEYA\svc_web
password = X7kQ2mP9vR4tW8zA
EOF
chmod 644 /opt/webapp/config.ini
