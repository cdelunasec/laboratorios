# http-headers: lo que falta

Estado: **esqueleto nada más**. Está en la rama `ccr-5caafaa2-03y3k8` (commit `53c59cd`). Todavía no hay ningún nivel jugable y `main` no se ha tocado.

## Lo que ya está hecho

### `http-headers/common/`

| Archivo | Qué hace |
|---|---|
| `Dockerfile.apache` | Base `php:8.2-apache`. Copia los archivos comunes y los de cada nivel, y corre `setup.sh` del nivel durante el build. No usa `apt-get`. |
| `entrypoint.sh` | Genera el flag una sola vez por contenedor y lo guarda en `/var/lib/lab/flag`. Corre `flag.sh` del nivel con el flag como `$1` (y `LAB_FIRST_BOOT=1` en el primer arranque) y escribe el SHA-256 del flag en `/lab-hashes/<NN>`. El flag nunca va en variables de entorno. |
| `php-base.ini` | `display_errors = On`, `expose_php = Off`, `allow_url_include = Off`, `register_argc_argv = Off` y `file_uploads = Off`. |
| `lab.php` | Plantilla común: `e()`, `lab_header($nivel, $app, $tagline, $pista)` (la pista va en el `<title>`, igual que en LFI), `lab_nav()`, `lab_footer($objetivo)`, `lab_alert()`, `lab_flag()` / `lab_show_flag()` (leen `/flag.txt`) y `lab_json()`. |
| `assets/style.css` | El CSS de LFI más clases extra: `form.stack`, `pre`, `table.data`, `.card` y `.flag`. |

### `http-headers/levelNN/` (del 01 al 20)

Cada carpeta trae los archivos por defecto. Hay que ajustarlos según el nivel:

- `docker-compose.yml`: puerto `127.0.0.1:82NN`, contenedor `http-headers-NN`, imagen `cdelunasec-labs/http-headers:NN`, volumen `../../.flags/http-headers:/lab-hashes` y `extra_hosts: host.docker.internal:host-gateway` (en todos, para no delatar qué nivel lo usa).
- `apache.conf`: el mismo de LFI nivel 1 (handler PHP, sin `Indexes`, `AllowOverride None`).
- `php.ini`: vacío, sin cambios sobre la base.
- `setup.sh`: vacío (`true`).
- `flag.sh`:
  - **Niveles 01 a 16 (lectura):** escribe el flag en `/flag.txt` (444) y la app lo entrega con `lab_show_flag()` cuando burlas el control.
  - **Niveles 17 a 20 (RCE):** escribe el flag en `/flag_XXXX.txt` con nombre aleatorio (444). Solo se encuentra listando el sistema.
- `src/`: **vacío** (git no lo guarda; hay que crearlo).

### Convenciones acordadas

- Categoría `http-headers`. Alias en `lab.sh`: `http`, `headers`, `cabeceras`.
- Puertos: `82` + nivel con dos dígitos (nivel 1 → `8201`, nivel 20 → `8220`).
- Formato del flag: `cdelunasec{http-headers-NN_<24 hex>}`.
- Hash del flag: `.flags/http-headers/NN` (`.flags/` ya está en `.gitignore`).
- Tipo de objetivo: mixto. Del 01 al 16 son de lectura y del 17 al 20 son de RCE.
- Sin write-ups, pistas extendidas, soluciones ni solvers en el repo. Si se hacen, van en `solutions/`, que está en `.gitignore`.

## Lo que falta

### 1. Las apps de los 20 niveles

Por cada nivel:

- [ ] Definir la técnica, el nombre de la app, la pista corta (va en el título) y el objetivo (va en `lab_footer`).
- [ ] Escribir `src/` (por lo menos `src/index.php`, que usa `require '/var/www/lab/lab.php';`).
- [ ] Ajustar `apache.conf`, `php.ini`, `setup.sh` y `flag.sh` si el nivel lo necesita. Si hacen falta módulos de Apache, se activan con `a2enmod` en `setup.sh`. Si hacen falta archivos fuera del webroot, se ponen en `src/_algo/` y `setup.sh` los mueve, porque el Dockerfile solo copia `src/`.
- [ ] Si el nivel necesita secretos aleatorios (contraseñas, tokens), generarlos en `flag.sh`, que corre en cada arranque. Se usa `LAB_FIRST_BOOT` para generarlos una sola vez.

Plantilla para ir llenando (después pasa al README):

| Nivel | Pista | Tipo | Puerto |
|---|---|---|---|
| 1 | | lectura | `8201` |
| 2 | | lectura | `8202` |
| 3 | | lectura | `8203` |
| 4 | | lectura | `8204` |
| 5 | | lectura | `8205` |
| 6 | | lectura | `8206` |
| 7 | | lectura | `8207` |
| 8 | | lectura | `8208` |
| 9 | | lectura | `8209` |
| 10 | | lectura | `8210` |
| 11 | | lectura | `8211` |
| 12 | | lectura | `8212` |
| 13 | | lectura | `8213` |
| 14 | | lectura | `8214` |
| 15 | | lectura | `8215` |
| 16 | | lectura | `8216` |
| 17 | | rce | `8217` |
| 18 | | rce | `8218` |
| 19 | | rce | `8219` |
| 20 | jefe final | rce | `8220` |

### 2. Panel central de validación de flags

Acordado: un solo panel para **http-headers + LFI**. LFI ya deja sus hashes en `.flags/lfi/NN`, así que no hay que tocar nada de LFI.

- [ ] `panel/Dockerfile`: base `php:8.2-apache` (ya está en caché). Puede correr `php -S 0.0.0.0:80 -t /app`, o Apache con un entrypoint que haga `chown www-data` sobre la carpeta de progreso.
- [ ] `panel/docker-compose.yml`: `127.0.0.1:8200:80`, contenedor `panel` y volumen `../.flags:/flags`. Los hashes se leen de `/flags/<categoría>/<NN>` y el progreso se escribe en `/flags/panel/progreso.json`.
- [ ] `panel/src/index.php`:
  - [ ] Un input para pegar el flag. Se detecta la categoría y el nivel con la regex `^cdelunasec\{([a-z][a-z-]*?)-(\d{2})_[0-9a-f]+\}$`.
  - [ ] Se compara `hash('sha256', $flag)` con el archivo del nivel usando `hash_equals()`. El panel nunca conoce el flag.
  - [ ] Mensajes:
    - correcto;
    - incorrecto;
    - el nivel nunca se ha levantado ("no hay hash: `./lab.sh up <cat> <n>`");
    - no coincide con el flag actual ("si reiniciaste el nivel, el flag cambió");
    - formato inválido.
  - [ ] Una cuadrícula por categoría con los 20 niveles: estado (resuelto / pendiente / sin levantar), pista, tipo, puerto y barra de progreso.
  - [ ] Guardar en `progreso.json` la fecha en que se resolvió cada nivel y el hash con el que se resolvió.
  - [ ] Un botón para reiniciar el progreso de una categoría, con confirmación.
  - [ ] Las categorías van en un arreglo de configuración (nombre, prefijo de puerto, cantidad de niveles, pistas y tipos). Así file-upload se agrega con una línea el día que su entrypoint también escriba hashes.
- [ ] Reutilizar el estilo de `common/assets/style.css`.

### 3. `lab.sh`

- [ ] `CATEGORIES`: agregar `[http]`, `[headers]`, `[cabeceras]` y `[http-headers]`, todos apuntando a `http-headers`.
- [ ] `PORT_PREFIX`: agregar `[http-headers]="82"`.
- [ ] En `up` y `reset`, cambiar `mkdir -p "$ROOT/.flags/lfi"` por `mkdir -p "$ROOT/.flags/$DIR_CAT"`. LFI sigue funcionando igual.
- [ ] Comando nuevo `panel`. `./lab.sh panel` (o `panel up`) hace `mkdir -p .flags/panel` y levanta el panel en `http://127.0.0.1:8200`. `./lab.sh panel down` lo apaga. Proyecto compose: `panel`.
- [ ] `status`: agregar `http-headers` y `panel` a la regex `^(file-upload|lfi)-`.
- [ ] `stop-all`: agregar `docker ps -q --filter "name=^http-headers-"`. El panel queda aparte.
- [ ] `usage`: agregar la categoría nueva y el comando `panel`.

### 4. `README.md`

- [ ] Cambiar `- [ ] http / cabeceras` por `- [x] **http-headers** — 20 niveles. ...`.
- [ ] Actualizar la línea "Categorías disponibles" con `http` (alias de `http-headers`).
- [ ] Sección `## http-headers`: puertos `8201`–`8220`, `./lab.sh up http 1`, dónde está el flag según el tipo (lectura: lo entrega la app; RCE: `/flag_XXXX.txt`) y la tabla Nivel / Pista / Tipo / Puerto.
- [ ] Nota general: desde dentro de los contenedores de http-headers, tu máquina se ve como `host.docker.internal`. Sirve si en algún nivel necesitas que el servidor te contacte.
- [ ] Sección `## Panel de flags`: `./lab.sh panel`, `http://127.0.0.1:8200`, qué categorías valida y que guarda el progreso en `.flags/panel/`.
- [ ] Nota de estado: si se probó en Docker o no, igual que en LFI.

### 5. Pruebas

- [ ] Por cada nivel:
  - [ ] Levantarlo con `./lab.sh up http N`.
  - [ ] Verificar que aparece `.flags/http-headers/NN`.
  - [ ] Verificar que el flag no sale en el código fuente ni en `/proc/self/environ`.
  - [ ] Verificar que el nivel se resuelve con la técnica prevista y que no tiene atajos.
  - [ ] Verificar que `reset` genera un flag nuevo.
- [ ] Panel: validar un flag bueno, uno malo, uno de un nivel reiniciado y uno de LFI.
- [ ] Los solvers de prueba, si se escriben, van solo en `solutions/`.

### Notas del entorno (sesión en la nube)

- Docker no arranca solo: hay que correr `dockerd &` y esperar a que `docker info` responda.
- `deb.debian.org` está bloqueado ahí, así que los Dockerfile de LFI y file-upload (que usan `apt-get`) no construyen en ese entorno. En Kali sí deberían.
- `archive.ubuntu.com` (http) y Docker Hub sí responden.
- La imagen `php:8.2-apache` (Apache 2.4.69) ya trae `curl`, `pdo_sqlite`, `sqlite3`, `SimpleXML`/`dom`, `session`, `htpasswd`, y `mod_cgi` disponible con `a2enmod`. Con eso los niveles no necesitan `apt-get`.
