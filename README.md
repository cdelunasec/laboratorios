# laboratorios

Laboratorios de vulnerabilidades web para practicar una técnica a la vez, por niveles de dificultad creciente. Cada nivel es una mini-app con una desconfiguración realista (de las que salen en pentesting y en CTFs), y se resuelve con explotación **real**: si logras tu objetivo, ejecutas comandos de verdad contra el servidor.

Pensado para repasar de forma aislada lo que en las máquinas aparece encadenado: montas el nivel, lo rompes, subes de nivel.

## Requisitos

- Docker y el plugin `docker compose`
- Linux (probado en Kali). Todo escucha solo en `127.0.0.1`.

```bash
sudo apt install docker.io docker-compose-plugin -y
```

> **Aviso:** estas apps son vulnerables a propósito e incluyen ejecución remota de comandos. Córrelas solo en tu máquina local. Nunca las expongas a una red.

## Uso

```bash
git clone https://github.com/cdelunasec/laboratorios.git
cd laboratorios
chmod +x lab.sh
```

```bash
./lab.sh up upload 1
```

Eso levanta el nivel 1 de file-upload en `http://127.0.0.1:8001`. El puerto es `80` + el número de nivel con dos dígitos (nivel 7 → `8007`, nivel 15 → `8015`).

| Comando | Qué hace |
|---|---|
| `./lab.sh up <cat> <n>` | Construye y levanta el nivel |
| `./lab.sh down <cat> <n>` | Lo apaga |
| `./lab.sh reset <cat> <n>` | Lo deja limpio y con flag nuevo |
| `./lab.sh logs <cat> <n>` | Muestra los logs del servidor |
| `./lab.sh status` | Niveles corriendo |
| `./lab.sh stop-all` | Apaga todo |
| `./lab.sh list` | Categorías y cuántos niveles tiene cada una |

| `./lab.sh panel` | Levanta el panel de flags en `http://127.0.0.1:8200` (`./lab.sh panel down` lo apaga) |

Categorías disponibles: `upload` (alias de `file-upload`), `lfi`, `http` (alias de `http-headers`) e `idor`.

## El objetivo

Cada nivel esconde un flag con el formato `cdelunasec{...}`. Dónde está depende del tipo de nivel:

- **RCE** (todo file-upload y parte de lfi y http-headers): en un archivo `/flag_XXXX.txt` con nombre aleatorio. No lo puedes adivinar: tienes que conseguir **ejecución de comandos**, listar el sistema de archivos y leerlo.
- **Lectura**: la propia app te lo entrega cuando burlas el control (o lo lees de una ruta conocida, en lfi). Cada categoría explica abajo dónde buscarlo.

El flag cambia cada vez que levantas o reinicias un nivel. Para comprobar si el tuyo es correcto usa el [panel de flags](#panel-de-flags).

Cada nivel corre en su propio contenedor aislado: lo que hagas en uno no afecta a los demás.

## Categorías

- [x] **file-upload** — 20 niveles. Subir un archivo arbitrario hasta lograr RCE, burlando defensas cada vez más fuertes.
- [x] **lfi** — 20 niveles. Local File Inclusion, de leer un archivo directo a encadenar LFI con RCE.
- [x] **http-headers** — 20 niveles. Cabeceras HTTP: de cambiar el User-Agent a envenenar enlaces por `Host` y lograr RCE desde una cabecera.
- [ ] xss
- [ ] ssrf
- [ ] csrf
- [x] **idor** — 12 niveles. Insecure Direct Object Reference: leer (y modificar) datos de otros usuarios, de cambiar un número a encadenar filtraciones en una API.
- [ ] web (cadena completa)

## file-upload

20 niveles, de subir un `.php` directo hasta burlar varias capas (lista negra + verificación de imagen + WAF + PHP endurecido). Cada nivel enseña un bypass real:

| Nivel | Lo que vas a burlar |
|---|---|
| 1 | Sin validación |
| 2 | Validación solo en el navegador (JavaScript) |
| 3 | Confía en el `Content-Type` |
| 4 | Lista negra incompleta de extensiones |
| 5 | Lista negra sensible a mayúsculas/minúsculas |
| 6 | Whitelist por regex sin anclar |
| 7 | Doble extensión + `AddHandler` mal puesto |
| 8 | Verificación de magic bytes |
| 9 | `getimagesize()` + GD |
| 10 | Permite subir `.htaccess` |
| 11 | Limpia la extensión una sola vez |
| 12 | Uploads sin ejecución + nombre controlable |
| 13 | Uploads sin ejecución + un LFI aparte |
| 14 | Valida después de guardar (race condition) |
| 15 | Reprocesa la imagen (pero no todas) |
| 16 | Extrae un ZIP (zip slip) |
| 17 | Nombre aleatorio pero predecible |
| 18 | Nginx + PHP-FPM con `cgi.fix_pathinfo` |
| 19 | WAF que inspecciona el contenido |
| 20 | Varias capas + `disable_functions` |

¿Trancado? Cada nivel tiene su pista y su writeup (no se publican en este repo).

## lfi

20 niveles de Local File Inclusion. Puerto `81` + el nivel con dos dígitos (nivel 1 → `8101`, nivel 20 → `8120`).

```bash
./lab.sh up lfi 1
```

Los niveles de **lectura** guardan el flag en una ruta conocida (normalmente `/flag.txt`, salvo que el objetivo del nivel diga otra cosa). Los niveles de **RCE** lo esconden en `/flag_XXXX.txt` con nombre aleatorio, así que necesitas ejecutar comandos para encontrarlo. La pista de cada nivel va en el título de la página.

| Nivel | Pista | Tipo | Puerto |
|---|---|---|---|
| 1 | sin filtros | lectura | `8101` |
| 2 | rutas absolutas bloqueadas | lectura | `8102` |
| 3 | directorio fijo | lectura | `8103` |
| 4 | extensión forzada | lectura | `8104` |
| 5 | filtro de una sola pasada | lectura | `8105` |
| 6 | ruta aprobada | lectura | `8106` |
| 7 | el filtro mira la URL cruda | lectura | `8107` |
| 8 | decodificado dos veces | lectura | `8108` |
| 9 | leer sin ejecutar | lectura | `8109` |
| 10 | credenciales en el código | lectura | `8110` |
| 11 | byte nulo (PHP viejo simulado) | lectura | `8111` |
| 12 | sin barra ni puntos | lectura | `8112` |
| 13 | datos en línea | rce | `8113` |
| 14 | el cuerpo de la petición | rce | `8114` |
| 15 | la bitácora del servidor | rce | `8115` |
| 16 | sesiones | rce | `8116` |
| 17 | archivos dentro de archivos | rce | `8117` |
| 18 | inclusión remota | rce | `8118` |
| 19 | mira las galletas | lectura | `8119` |
| 20 | jefe final | rce | `8120` |

> Estado: generado pero todavía sin probar en Docker. Si un nivel no levanta o no se comporta como dice su pista, abre un issue.

## http-headers

20 niveles sobre cabeceras HTTP. Puerto `82` + el nivel con dos dígitos (nivel 1 → `8201`, nivel 20 → `8220`).

```bash
./lab.sh up http 1
```

En los niveles de **lectura** la propia app te entrega el flag cuando burlas el control (lo verás en la página o en la respuesta de la API). En los de **RCE** el flag está en `/flag_XXXX.txt` con nombre aleatorio, así que tienes que ejecutar comandos para encontrarlo. La pista de cada nivel va en el título de la página.

Algunos niveles responden distinto según el nombre de host que mandes: no hace falta tocar `/etc/hosts`, basta con la cabecera `Host` (`curl -H 'Host: ...'`).

| Nivel | Pista | Tipo | Puerto |
|---|---|---|---|
| 1 | dime con qué navegas | lectura | `8201` |
| 2 | ¿de dónde vienes? | lectura | `8202` |
| 3 | solo desde la oficina | lectura | `8203` |
| 4 | el proxy no es el único que habla | lectura | `8204` |
| 5 | verbos | lectura | `8205` |
| 6 | el WAF solo deja GET y POST | lectura | `8206` |
| 7 | un servidor, varios sitios | lectura | `8207` |
| 8 | la sesión vive en tu navegador | lectura | `8208` |
| 9 | negociación de contenido | lectura | `8209` |
| 10 | lee las cabeceras de la respuesta | lectura | `8210` |
| 11 | Authorization: Bearer | lectura | `8211` |
| 12 | descarga por partes | lectura | `8212` |
| 13 | el enlace lo arma tu petición | lectura | `8213` |
| 14 | la URL original | lectura | `8214` |
| 15 | límite de intentos | lectura | `8215` |
| 16 | todo lo anterior | lectura | `8216` |
| 17 | estadísticas de navegador | rce | `8217` |
| 18 | a ciegas | rce | `8218` |
| 19 | galleta serializada | rce | `8219` |
| 20 | jefe final | rce | `8220` |

> Desde dentro de los contenedores de http-headers, tu máquina se ve como `host.docker.internal`. Te sirve en los niveles donde necesitas que el servidor te contacte a ti (levanta un `nc -lvnp 9000` o un `python3 -m http.server 9000` y apunta ahí).

> Estado: los 20 niveles se probaron en Docker con un solver automático que los resuelve con la técnica prevista y valida el flag contra el hash del nivel (20/20).

## idor

12 niveles de IDOR (Insecure Direct Object Reference). Puerto `83` + el nivel con dos dígitos (nivel 1 → `8301`, nivel 12 → `8312`).

```bash
./lab.sh up idor 1
```

En todos entras con tu propia cuenta, `carlos` / `carlos123`, y el objetivo es llegar a datos que no son tuyos (casi siempre del administrador). El flag está escondido en esos datos: cuando lo encuentras, lo pegas en el panel. La pista de cada nivel va en el título de la página.

| Nivel | Pista | Puerto |
|---|---|---|
| 1 | cambia el número | `8301` |
| 2 | lo oculto en el formulario | `8302` |
| 3 | enumera | `8303` |
| 4 | codificado no es cifrado | `8304` |
| 5 | un hash predecible | `8305` |
| 6 | identificadores que se filtran | `8306` |
| 7 | no solo se lee | `8307` |
| 8 | dos veces el mismo parámetro | `8308` |
| 9 | versiones viejas | `8309` |
| 10 | archivos generados | `8310` |
| 11 | funciones, no objetos | `8311` |
| 12 | jefe final | `8312` |

> Estado: los 12 niveles se probaron en Docker con un solver automático que los resuelve con la técnica prevista y valida el flag contra el hash del nivel (12/12).

## Panel de flags

```bash
./lab.sh panel
```

Abre `http://127.0.0.1:8200`, pega un flag y el panel te dice si es el del nivel. Valida **lfi**, **http-headers** e **idor**. Nunca conoce los flags: cada nivel, al arrancar, deja el SHA-256 de su flag en `.flags/<categoría>/<NN>` y el panel compara contra eso. Si reinicias un nivel el flag cambia, así que un flag viejo deja de valer.

El progreso (qué resolviste y cuándo) se guarda en `.flags/panel/progreso.json`. Cada categoría tiene un botón para reiniciar su progreso.
