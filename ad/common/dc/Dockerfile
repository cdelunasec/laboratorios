# ---------------------------------------------------------------------------
# Etapa 1: recompila el KDC de Samba (Heimdal) con el parche de compatibilidad
# con Windows. Samba trae un KDC Heimdal que es más estricto que el de Windows
# en cuatro detalles de los que dependen las herramientas de Kerberos de Kali
# (impacket: getTGT, getST/delegación, GetNPUsers/AS-REP roasting y
# GetUserSPNs/Kerberoasting). El parche los relaja; ver
# common/dc/heimdal-windows-compat.patch. Reconstruye SOLO las dos librerías
# afectadas (libkdc y libdb-glue) a partir del mismo código fuente del paquete
# de Debian, así que el resto de Samba queda igual que el paquete.
# ---------------------------------------------------------------------------
FROM debian:trixie-slim AS kdc-build
ENV DEBIAN_FRONTEND=noninteractive
# Sin `rm` de /var/lib/apt/lists: el `apt-get source` de abajo necesita el índice
# de fuentes. La etapa es desechable, no pesa en la imagen final.
RUN sed -i 's/^Types: deb$/Types: deb deb-src/' /etc/apt/sources.list.d/debian.sources \
    && apt-get update \
    && apt-get build-dep -y samba \
    && apt-get install -y --no-install-recommends patch patchelf
WORKDIR /src
RUN apt-get source samba
COPY common/dc/heimdal-windows-compat.patch /src/heimdal-windows-compat.patch
RUN cd samba-*/ \
    && patch -p1 < /src/heimdal-windows-compat.patch \
    && DEB_BUILD_OPTIONS=nocheck debian/rules configure \
    && PYTHONHASHSEED=1 ./buildtools/bin/waf build -j"$(nproc)" --targets=kdc,db-glue \
    && KDC=$(find bin -name 'libkdc-private-samba.so' | head -1) \
    && DBG=$(find bin -name 'libdb-glue-private-samba.so' | head -1) \
    && patchelf --set-rpath /usr/lib/x86_64-linux-gnu/samba "$KDC" \
    && patchelf --set-rpath /usr/lib/x86_64-linux-gnu/samba "$DBG" \
    && cp "$KDC" /libkdc-private-samba.so.0 \
    && cp "$DBG" /libdb-glue-private-samba.so.0

# ---------------------------------------------------------------------------
# Etapa 2: la imagen del DC. Instala Samba del paquete y luego sustituye SOLO
# la librería del KDC por la recompilada arriba (misma versión de Samba).
# ---------------------------------------------------------------------------
FROM debian:trixie-slim

# LAB LOCAL INTENCIONALMENTE VULNERABLE (Active Directory con Samba 4).
# Entorno controlado de práctica: vive en una red interna de Docker. No exponer a ninguna red.

ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        samba samba-ad-dc samba-ad-provision samba-dsdb-modules samba-vfs-modules \
        winbind krb5-user ldb-tools python3-samba smbclient ldap-utils \
        procps acl ca-certificates \
    && rm -rf /var/lib/apt/lists/*

COPY --from=kdc-build /libkdc-private-samba.so.0 /usr/lib/x86_64-linux-gnu/samba/libkdc-private-samba.so.0
COPY --from=kdc-build /libdb-glue-private-samba.so.0 /usr/lib/x86_64-linux-gnu/samba/libdb-glue-private-samba.so.0

COPY common/dc/entrypoint.sh /usr/local/bin/lab-entrypoint
COPY common/dc/lib.sh /lab/lib.sh

ARG LEVEL
ENV LAB_ID=ad-${LEVEL} LAB_LEVEL=${LEVEL}

COPY level${LEVEL}/ /lab/level/

RUN find /usr/local/bin/lab-entrypoint /lab -type f \( -name '*.sh' -o -name 'lab-entrypoint' \) -exec sed -i 's/\r$//' {} + \
    && chmod +x /usr/local/bin/lab-entrypoint \
    && chmod -R go-rwx /lab

ENTRYPOINT ["lab-entrypoint"]
