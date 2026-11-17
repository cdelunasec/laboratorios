# ---------------------------------------------------------------------------
# srv01: servidor Linux miembro del dominio (quisqueya.lab). Lo usan los
# niveles de foothold, tickets y delegación (16-20). Se une al dominio en el
# primer arranque con `net ads join` (Samba), usando la contraseña de
# Administrator que el DC deja en el volumen compartido /lab-join, y levanta SSH
# para que los jugadores entren con sus cuentas de AD (password o GSSAPI).
#
# LAB LOCAL INTENCIONALMENTE VULNERABLE. Entorno controlado de práctica: vive
# en una red interna de Docker. No exponer a ninguna red.
# ---------------------------------------------------------------------------
FROM debian:trixie-slim
ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        samba-common-bin winbind libnss-winbind libpam-winbind \
        krb5-user smbclient openssh-server sudo procps python3 ldap-utils \
    && rm -rf /var/lib/apt/lists/*

COPY common/srv/entrypoint.sh /usr/local/bin/srv-entrypoint

ARG LEVEL
ENV LAB_LEVEL=${LEVEL}

COPY level${LEVEL}/ /lab/level/

RUN find /lab -type f -name '*.sh' -exec sed -i 's/\r$//' {} + \
    && sed -i 's/\r$//' /usr/local/bin/srv-entrypoint \
    && chmod +x /usr/local/bin/srv-entrypoint \
    && chmod -R go-rwx /lab

ENTRYPOINT ["srv-entrypoint"]
