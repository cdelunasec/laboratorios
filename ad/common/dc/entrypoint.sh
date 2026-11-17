#!/bin/bash
# Lab local de Active Directory (entorno controlado). En el primer arranque del
# contenedor provisiona un dominio Samba nuevo con contraseña de Administrator
# aleatoria, aplica la desconfiguración del nivel (level/setup.sh), genera el
# flag y deja su SHA-256 en /lab-hashes para el panel local.
set -euo pipefail

source /lab/lib.sh

STATE=/var/lib/lab
mkdir -p "$STATE"
chmod 700 "$STATE"

# El volumen /lab-join puede traer restos de una corrida anterior: un join
# fallido deja `ready` y `adminpass` viejos que ya no valen. Se limpian en cada
# arranque (el DC los regenera más abajo).
rm -f /lab-join/ready /lab-join/adminpass /lab-join/flag

if [[ ! -f "$STATE/provisioned" ]]; then
    log "Provisionando el dominio $LAB_REALM (tarda un poco la primera vez)..."
    rm -f /etc/samba/smb.conf
    rand_pass > "$STATE/adminpass"
    # El provisionado escupe cientos de líneas; van a un log y solo se
    # muestran si falla.
    if ! samba-tool domain provision \
        --realm="$LAB_REALM" --domain="$LAB_DOMAIN" \
        --server-role=dc --dns-backend=SAMBA_INTERNAL \
        --host-name="$LAB_DC_NAME" --host-ip="$LAB_DC_IP" \
        --adminpass="$(cat "$STATE/adminpass")" \
        --option="acl_xattr:security_acl_name = user.NTACL" \
        --use-rfc2307 > "$STATE/provision.log" 2>&1; then
        tail -40 "$STATE/provision.log" >&2
        log "Falló el provisionado del dominio."
        exit 1
    fi
    cp /var/lib/samba/private/krb5.conf /etc/krb5.conf

    # Ajustes de laboratorio: binds LDAP simples sin TLS y contraseñas débiles
    # permitidas (cada nivel decide cuáles usa).
    smb_global "ldap server require strong auth = no"
    smb_global "log level = 1"
    samba-tool domain passwordsettings set -H "$SAM" \
        --complexity=off --history-length=0 --min-pwd-age=0 \
        --max-pwd-age=0 --min-pwd-length=1 >/dev/null

    base_users
    log "Aplicando la configuración del nivel $LAB_LEVEL..."
    # shellcheck disable=SC1091
    source /lab/level/setup.sh
    # Los archivos que el nivel dejó en SYSVOL/NETLOGON necesitan sus ACLs NT.
    samba-tool ntacl sysvolreset >/dev/null 2>&1 || true
    touch "$STATE/provisioned"
fi

if [[ ! -f "$STATE/flag" ]]; then
    echo "cdelunasec{${LAB_ID}_$(rand_hex 12)}" > "$STATE/flag"
    chmod 600 "$STATE/flag"
fi
if [[ "${LAB_FLAG_ON:-dc}" == dc ]]; then
    mkdir -p /srv/flag
    cp "$STATE/flag" /srv/flag/flag.txt
    chmod 755 /srv/flag
    chmod 644 /srv/flag/flag.txt
fi

samba --foreground --no-process-group &
SAMBA_PID=$!
trap 'kill $SAMBA_PID 2>/dev/null; wait $SAMBA_PID' TERM INT

wait_ldap
if [[ -f /lab/level/live.sh && ! -f "$STATE/live" ]]; then
    # shellcheck disable=SC1091
    source /lab/level/live.sh
    touch "$STATE/live"
fi
share_join_secret

# El DC siempre conoce el flag (aunque lo aloje srv01), así que siempre deja
# el hash para el panel.
if [[ -d /lab-hashes ]]; then
    tr -d '\n' < "$STATE/flag" | sha256sum | cut -d' ' -f1 > "/lab-hashes/$LAB_LEVEL"
    chmod 644 "/lab-hashes/$LAB_LEVEL"
fi
log "Listo: $LAB_DC_NAME.$LAB_DNS ($LAB_DC_IP)"

wait $SAMBA_PID
