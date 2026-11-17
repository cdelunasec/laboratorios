# El flag vive en el home del usuario con el que entras por SSH.
uid="$(getent passwd svc_backup | cut -d: -f3)"
gid="$(getent passwd svc_backup | cut -d: -f4)"
mkdir -p /home/svc_backup
chown "${uid:-0}:${gid:-0}" /home/svc_backup
mv /lab-join/flag /home/svc_backup/flag.txt
chown "${uid:-0}:${gid:-0}" /home/svc_backup/flag.txt
chmod 600 /home/svc_backup/flag.txt
