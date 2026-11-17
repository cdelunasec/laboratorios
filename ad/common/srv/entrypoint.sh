#!/bin/bash
# srv01: se une al dominio (quisqueya.lab) con `net ads join` en el primer
# arranque y levanta SSH. Espera a que el DC termine de provisionar y deje la
# contraseña de Administrator en /lab-join; después arranca winbindd (no hay
# systemd en el contenedor), corre level/srv.sh, coloca el flag si el nivel lo
# aloja aquí y levanta sshd en primer plano.
set -euo pipefail

LAB_REALM="${LAB_REALM:-QUISQUEYA.LAB}"
LAB_DNS="${LAB_REALM,,}"
LAB_DOMAIN="${LAB_DOMAIN:-QUISQUEYA}"
LAB_DC_IP="${LAB_DC_IP:-10.13.37.10}"

log() { echo "[srv] $*"; }

# DNS y Kerberos apuntando al DC.
cat > /etc/resolv.conf <<EOF
search $LAB_DNS
nameserver $LAB_DC_IP
EOF
cat > /etc/krb5.conf <<EOF
[libdefaults]
 default_realm = $LAB_REALM
 dns_lookup_kdc = true
 rdns = false

[realms]
 $LAB_REALM = {
  kdc = $LAB_DC_IP
 }
EOF

# smb.conf de servidor miembro (winbind).
cat > /etc/samba/smb.conf <<EOF
[global]
   workgroup = $LAB_DOMAIN
   realm = $LAB_REALM
   security = ads
   kerberos method = secrets and keytab
   server string = %h (lab)
   winbind use default domain = yes
   winbind refresh tickets = yes
   idmap config * : backend = tdb
   idmap config * : range = 10000-19999
   idmap config $LAB_DOMAIN : backend = rid
   idmap config $LAB_DOMAIN : range = 20000-29999
   template shell = /bin/bash
   template homedir = /home/%U
   winbind enum users = no
   winbind enum groups = no
EOF

# Si el contenedor se reinicia solo (mismo volumen de datos), ya está unido:
# no hay `ready` que esperar ni que volver a unirse.
if net ads testjoin >/dev/null 2>&1; then
    log "Ya estaba unido a $LAB_REALM"
else
    # Espera a que el DC termine de provisionar y deje la clave de unión.
    while [[ ! -f /lab-join/ready ]]; do sleep 1; done

    # Únete al dominio. `net ads join` prueba Kerberos y, si el KDC no acepta la
    # preautenticación, cae a NTLM (que tarda un poco más en estar listo).
    # Releemos la contraseña en cada intento: si el DC reprovisiona, cambia.
    joined=0
    for _ in $(seq 1 60); do
        ADMINPASS="$(cat /lab-join/adminpass 2>/dev/null || true)"
        if [[ -n "$ADMINPASS" ]] && net ads join -U "Administrator%$ADMINPASS" -S "dc01.$LAB_DNS" >/tmp/join.log 2>&1; then
            joined=1
            break
        fi
        sleep 3
    done
    if [[ "$joined" != 1 ]]; then
        log "No pude unirme al dominio $LAB_REALM"
        tail -40 /tmp/join.log >&2
        exit 1
    fi
    log "Unido a $LAB_REALM"

    # El join por NTLM no deja keytab del host; la generamos para que sshd acepte
    # logins GSSAPI (host/srv01.<dominio>).
    net ads keytab create >/dev/null 2>&1 || true
fi

# Ya no hace falta el secreto de unión.
rm -f /lab-join/adminpass /lab-join/ready

# nsswitch: resolver usuarios/grupos de AD por winbind.
for db in passwd group; do
    grep -q "^$db:.*winbind" /etc/nsswitch.conf 2>/dev/null \
        || sed -i "s/^$db:\(.*\)$/$db:\1 winbind/" /etc/nsswitch.conf
done

# PAM: autenticación y cuenta por winbind, y home automático al entrar por SSH.
grep -q 'pam_winbind.so' /etc/pam.d/common-auth 2>/dev/null \
    || sed -i '1i auth    [success=1 default=ignore]      pam_winbind.so krb5_auth krb5_ccache_type=FILE' /etc/pam.d/common-auth
grep -q 'pam_winbind.so' /etc/pam.d/common-account 2>/dev/null \
    || sed -i '1i account [success=1 default=ignore]      pam_winbind.so' /etc/pam.d/common-account
grep -q 'pam_mkhomedir.so' /etc/pam.d/common-session 2>/dev/null \
    || echo 'session required pam_mkhomedir.so skel=/etc/skel umask=0077' >> /etc/pam.d/common-session

# Arranca winbindd (no hay systemd). Espera a que resuelva cuentas de AD.
winbindd -D 2>/tmp/wb.err || true
for _ in $(seq 1 30); do
    getent passwd Administrator >/dev/null 2>&1 && break
    sleep 1
done

mkdir -p /run/sshd

# sshd solo con GSSAPI (tickets Kerberos): sin contraseña, para que el foothold
# sea de verdad el keytab expuesto y no la contraseña de la cuenta.
grep -q '^GSSAPIAuthentication yes' /etc/ssh/sshd_config 2>/dev/null \
    || printf '\n# Lab: GSSAPI para entrar con tickets Kerberos\nGSSAPIAuthentication yes\nGSSAPICleanupCredentials yes\n' >> /etc/ssh/sshd_config
grep -q '^PasswordAuthentication no' /etc/ssh/sshd_config 2>/dev/null \
    || printf '\n# Lab: solo GSSAPI, nada de contraseña\nPasswordAuthentication no\nKbdInteractiveAuthentication no\n' >> /etc/ssh/sshd_config

# Configuración del nivel (srv.sh) y colocación del flag.
if [[ -f /lab/level/srv.sh ]]; then
    # shellcheck disable=SC1091
    source /lab/level/srv.sh
fi
if [[ -f /lab-join/flag ]]; then
    install -m 600 -o root -g root /lab-join/flag /root/flag.txt
    rm -f /lab-join/flag
fi

log "Listo: srv01 ($LAB_DNS)"
exec /usr/sbin/sshd -D
