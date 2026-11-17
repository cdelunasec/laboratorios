#!/bin/bash
# Funciones para los setup.sh de cada nivel. Trabajan directo sobre sam.ldb
# (antes de arrancar samba) salvo las que dicen lo contrario.

LAB_REALM="${LAB_REALM:-QUISQUEYA.LAB}"
LAB_DOMAIN="${LAB_DOMAIN:-QUISQUEYA}"
LAB_DC_NAME="${LAB_DC_NAME:-dc01}"
LAB_DC_IP="${LAB_DC_IP:-10.13.37.10}"
LAB_DNS="${LAB_REALM,,}"
BASE_DN="DC=${LAB_DNS//./,DC=}"
SAM=/var/lib/samba/private/sam.ldb
SMBCONF=/etc/samba/smb.conf

log() { echo "[lab] $*"; }
rand_hex() { head -c "$1" /dev/urandom | od -An -tx1 | tr -d ' \n'; }
rand_pass() { echo "Lab-$(rand_hex 10)-Q9"; }

smb_global() { sed -i "/^\[global\]/a\\	$1" "$SMBCONF"; }

# share <nombre> <ruta> [líneas extra de smb.conf...]
share() {
    local name="$1" path="$2"; shift 2
    mkdir -p "$path"
    {
        echo
        echo "[$name]"
        echo "	path = $path"
        echo "	read only = yes"
        local line
        for line in "$@"; do echo "	$line"; done
    } >> "$SMBCONF"
}

# El share del flag: solo entra quien diga el nivel (usuarios o @grupos).
flag_share() {
    share Flag /srv/flag "valid users = $*" "browseable = yes"
}

user_add() {
    local name="$1" pass="$2"; shift 2
    samba-tool user create "$name" "$pass" -H "$SAM" "$@" >/dev/null
}
group_add() { samba-tool group add "$1" -H "$SAM" "${@:2}" >/dev/null; }
member_add() { local g="$1"; shift; samba-tool group addmembers "$g" "$(IFS=,; echo "$*")" -H "$SAM" >/dev/null; }
computer_add() { samba-tool computer create "$1" -H "$SAM" "${@:2}" >/dev/null; }
spn_add() { samba-tool spn add "$2" "$1" -H "$SAM" >/dev/null; }

dn_of() {
    ldbsearch -H "$SAM" "(sAMAccountName=$1)" dn 2>/dev/null | sed -n 's/^dn: //p' | head -1
}
sid_of() {
    ldbsearch -H "$SAM" "(sAMAccountName=$1)" objectSid 2>/dev/null | sed -n 's/^objectSid: //p' | head -1
}

# ldif <texto LDIF con changetype>
ldif() { printf '%s\n' "$1" | ldbmodify -H "$SAM" >/dev/null; }

# attr_set <sAMAccountName|DN> <atributo> <valor>
attr_set() {
    local dn="$1"
    [[ "$dn" == *=* ]] || dn="$(dn_of "$1")"
    ldif "dn: $dn
changetype: modify
replace: $2
$2: $3"
}

# uac_add <sAMAccountName> <bit decimal>  (4194304 = DONT_REQ_PREAUTH,
# 16777216 = TRUSTED_TO_AUTH_FOR_DELEGATION, 524288 = TRUSTED_FOR_DELEGATION)
uac_add() {
    local cur
    cur="$(ldbsearch -H "$SAM" "(sAMAccountName=$1)" userAccountControl | sed -n 's/^userAccountControl: //p')"
    attr_set "$1" userAccountControl "$(( cur | $2 ))"
}

# ace <objeto (sAMAccountName o DN)> <ACE en SDDL con %SID% donde va el principal> <principal>
ace() {
    local dn="$1" sddl="$2" sid
    [[ "$dn" == *=* ]] || dn="$(dn_of "$1")"
    sid="$(sid_of "$3")"
    [[ -n "$sid" ]] || { echo "[lab] no existe el principal $3" >&2; return 1; }
    samba-tool dsacl set -H "$SAM" --objectdn="$dn" --sddl="${sddl//%SID%/$sid}" >/dev/null
}

# Atajos de derechos (los nombres son los que muestra BloodHound).
GENERIC_ALL='(A;;CCDCLCSWRPWPDTLOCRSDRCWDWO;;;%SID%)'
GENERIC_WRITE='(A;;RPWPSWRC;;;%SID%)'
WRITE_DACL='(A;;WDRC;;;%SID%)'
WRITE_OWNER='(A;;WORC;;;%SID%)'
FORCE_CHANGE_PWD='(OA;;CR;00299570-246d-11d0-a768-00aa006e0529;;%SID%)'
ADD_SELF='(OA;;SW;bf9679c0-0de6-11d0-a285-00aa003049e2;;%SID%)'
ADD_MEMBER='(OA;;WP;bf9679c0-0de6-11d0-a285-00aa003049e2;;%SID%)'
DCSYNC_1='(OA;;CR;1131f6aa-9c07-11d1-f79f-00c04fc2dcd2;;%SID%)'
DCSYNC_2='(OA;;CR;1131f6ad-9c07-11d1-f79f-00c04fc2dcd2;;%SID%)'

# Empleados de relleno para que la enumeración no sea trivial. Contraseñas
# aleatorias: ninguno es el camino.
FILLER_USERS=(
    "mrodriguez:María:Rodríguez:Contabilidad"
    "jperez:José:Pérez:Ventas"
    "lgarcia:Luisa:García:Recursos Humanos"
    "rmartinez:Ramón:Martínez:Almacén"
    "acastillo:Ana:Castillo:Ventas"
    "fsantana:Félix:Santana:Logística"
    "ymejia:Yokasta:Mejía:Contabilidad"
    "pjimenez:Pedro:Jiménez:Soporte"
    "creyes:Carmen:Reyes:Dirección"
    "dnunez:Daniel:Núñez:Soporte"
    "esanchez:Elena:Sánchez:Compras"
    "hvasquez:Héctor:Vásquez:Almacén"
)
base_users() {
    local entry sam given sur dept
    for entry in "${FILLER_USERS[@]}"; do
        IFS=: read -r sam given sur dept <<< "$entry"
        user_add "$sam" "$(rand_pass)" --given-name="$given" --surname="$sur" \
            --department="$dept" --description="$dept"
    done
}

# Cuenta de partida de los niveles "con credenciales" (assumed breach). El
# README le da al jugador este usuario y contraseña.
STARTER_USER=pasante
STARTER_PASS=Bienvenido2026
starter_user() {
    user_add "$STARTER_USER" "$STARTER_PASS" --given-name="Pasante" \
        --surname="TI" --department="TI" --description="Pasante de TI"
}

# Lectura anónima por LDAP (sesión nula): fLDAPBlockAnonOps=2 en dSHeuristics y
# permiso de lectura para ANONYMOUS LOGON en todo el dominio.
anon_ldap() {
    ldif "dn: CN=Directory Service,CN=Windows NT,CN=Services,CN=Configuration,$BASE_DN
changetype: modify
replace: dSHeuristics
dSHeuristics: 0000002"
    samba-tool dsacl set -H "$SAM" --objectdn="$BASE_DN" \
        --sddl='(A;CI;RPLCLORC;;;AN)' >/dev/null
}

# Shares de invitado: cualquier usuario inexistente entra como invitado.
guest_on() {
    grep -q "map to guest" "$SMBCONF" || smb_global "map to guest = Bad User"
}

SYSVOL="/var/lib/samba/sysvol/$LAB_DNS"
NETLOGON="$SYSVOL/scripts"

# gpp_cpassword <clave>: la cifra como las Group Policy Preferences, con la clave
# AES-256 que Microsoft publicó (MS14-025). gpp-decrypt la descifra.
gpp_cpassword() {
    printf '%s' "$1" | iconv -f utf-8 -t utf-16le \
        | openssl enc -aes-256-cbc -nosalt \
            -K 4e9906e8fcb66cc9faf49310620ffee8f496e806cc057990209b09a433b66c1b \
            -iv 00000000000000000000000000000000 \
        | base64 -w0 | tr -d '='
}

# gpp_groups <guid de la GPO> <nombre visible> <usuario> <clave>: deja un
# Groups.xml de GPP con la contraseña "cifrada", como las GPO viejas.
gpp_groups() {
    local guid="$1" name="$2" user="$3" pass="$4" dir
    dir="$SYSVOL/Policies/{$guid}"
    mkdir -p "$dir/Machine/Preferences/Groups" "$dir/User"
    printf '[General]\r\nVersion=1\r\ndisplayName=%s\r\n' "$name" > "$dir/GPT.INI"
    cat > "$dir/Machine/Preferences/Groups/Groups.xml" <<EOF
<?xml version="1.0" encoding="utf-8"?>
<Groups clsid="{3125E937-EB16-4b4c-9934-544FC6D24D26}"><User clsid="{DF5F1855-51E5-4d24-8B1A-D9BDE98BA1D1}" name="$user" image="2" changed="2025-03-14 09:12:44" uid="{8A3B7D52-1E44-4B8F-9C1A-6F0E2D5A7B31}"><Properties action="U" newName="" fullName="" description="Cuenta de respaldos" cpassword="$(gpp_cpassword "$pass")" changeLogon="0" noChange="1" neverExpires="1" acctDisabled="0" userName="$user"/></User></Groups>
EOF
}

# gmsa_add <nombre sin $> <dnsHostName> <principal que puede leer la clave>
gmsa_add() {
    samba-tool service-account create -H "$SAM" --name="$1" \
        --dns-host-name="$2" >/dev/null
    # Las gMSA nacen con ACCOUNTDISABLE; sin esto no se puede autenticar con
    # ellas (KDC_ERR_CLIENT_REVOKED / STATUS_ACCOUNT_DISABLED).
    samba-tool user enable "$1\$" -H "$SAM" >/dev/null
    samba-tool service-account group-msa-membership add -H "$SAM" \
        --name="$1\$" --principal="$3" >/dev/null
}

# pre2k_computer <NOMBRE>: cuenta de equipo "creada como pre-Windows 2000", con
# la contraseña por defecto (el nombre en minúsculas) y PASSWD_NOTREQD.
pre2k_computer() {
    local name="$1" lower
    lower="$(echo "$1" | tr '[:upper:]' '[:lower:]')"
    computer_add "$name"
    samba-tool user setpassword "$name\$" -H "$SAM" --newpassword="$lower" >/dev/null
    attr_set "$name\$" userAccountControl 4128
}

# --- Funciones para live.sh (samba ya corriendo) ---

wait_ldap() {
    local i
    for i in $(seq 1 120); do
        ldapsearch -x -H ldap://127.0.0.1 -s base -b "" namingContexts >/dev/null 2>&1 && return 0
        sleep 1
    done
    log "samba no respondió por LDAP"; return 1
}

admin_pass() { cat /var/lib/lab/adminpass; }

# Si el nivel trae servidor miembro, le deja cómo unirse en el volumen interno
# (no accesible desde la red). srv01 lo borra al unirse. Si el flag lo aloja
# srv01 (LAB_FLAG_ON=srv), también se lo pasa por aquí.
share_join_secret() {
    [[ -d /lab-join ]] || return 0
    umask 077
    admin_pass > /lab-join/adminpass
    if [[ "${LAB_FLAG_ON:-dc}" == srv ]]; then
        cat /var/lib/lab/flag > /lab-join/flag
    fi
    : > /lab-join/ready
}

# keytab_expose <usuario> <ruta-salida>: exporta el keytab del usuario y lo deja
# legible. Requiere samba corriendo (se usa en live.sh). srv01 necesita el
# keytab del DC para aceptar logins GSSAPI; los niveles de foothold dejan uno
# "olvidado" en un share.
keytab_expose() {
    local user="$1" out="$2"
    samba-tool domain exportkeytab "$out" --principal="$user" >/dev/null 2>&1
    chmod 644 "$out"
}
