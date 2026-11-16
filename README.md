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

| `./lab.sh bot` | Levanta el bot víctima y el servidor de exploits en `http://127.0.0.1:8290` (los niveles que lo usan lo levantan solos) |
| `./lab.sh panel` | Levanta el panel de flags en `http://127.0.0.1:8200` (`./lab.sh panel down` lo apaga) |

Categorías disponibles: `upload` (alias de `file-upload`), `lfi`, `http` (alias de `http-headers`), `idor`, `ssrf`, `xss`, `csrf` y `web`.

## El objetivo

Cada nivel esconde un flag con el formato `cdelunasec{...}`. Dónde está depende del tipo de nivel:

- **RCE** (todo file-upload y web, y parte de lfi y http-headers): en un archivo `/flag_XXXX.txt` con nombre aleatorio. No lo puedes adivinar: tienes que conseguir **ejecución de comandos**, listar el sistema de archivos y leerlo.
- **Lectura**: la propia app te lo entrega cuando burlas el control (o lo lees de una ruta conocida, en lfi). Cada categoría explica abajo dónde buscarlo.

El flag cambia cada vez que levantas o reinicias un nivel. Para comprobar si el tuyo es correcto usa el [panel de flags](#panel-de-flags).

Cada nivel corre en su propio contenedor aislado: lo que hagas en uno no afecta a los demás.

## Categorías

- [x] **file-upload** — 20 niveles. Subir un archivo arbitrario hasta lograr RCE, burlando defensas cada vez más fuertes.
- [x] **lfi** — 20 niveles. Local File Inclusion, de leer un archivo directo a encadenar LFI con RCE.
- [x] **http-headers** — 20 niveles. Cabeceras HTTP: de cambiar el User-Agent a envenenar enlaces por `Host` y lograr RCE desde una cabecera.
- [x] **xss** — 15 niveles. Cross-Site Scripting con un admin de verdad (un navegador headless) como víctima: reflejado, almacenado, DOM, filtros, CSP y cookies HttpOnly.
- [x] **ssrf** — 12 niveles. Server-Side Request Forgery: hacer que el servidor pida por ti, de `127.0.0.1` a gopher, XXE y metadatos de la nube.
- [x] **csrf** — 12 niveles. Cross-Site Request Forgery contra un admin de verdad (el mismo bot): sin protección, tokens mal validados, Referer, SameSite, JSON y CORS.
- [x] **idor** — 12 niveles. Insecure Direct Object Reference: leer (y modificar) datos de otros usuarios, de cambiar un número a encadenar filtraciones en una API.
- [x] **web** — 3 aplicaciones completas (de momento). Cada una tiene varias fallas que hay que encadenar, como en una máquina, hasta ejecutar comandos.

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

## ssrf

12 niveles de SSRF (Server-Side Request Forgery). Puerto `84` + el nivel con dos dígitos (nivel 1 → `8401`, nivel 12 → `8412`).

```bash
./lab.sh up ssrf 1
```

Cada nivel tiene un servicio interno que solo escucha dentro del servidor (en `127.0.0.1`) y es el único que conoce el flag. Desde tu máquina no lo puedes tocar: tienes que conseguir que la app le haga la petición por ti. La página de cada nivel te dice dónde está ese servicio; la excepción es el nivel 3, donde el flag está en `/flag.txt`.

| Nivel | Pista | Puerto |
|---|---|---|
| 1 | el servidor pide por ti | `8401` |
| 2 | localhost tiene muchos nombres | `8402` |
| 3 | no solo http | `8403` |
| 4 | ¿qué puerto? | `8404` |
| 5 | lo que va antes de la arroba | `8405` |
| 6 | sígueme | `8406` |
| 7 | solo te dice si funcionó | `8407` |
| 8 | otro protocolo, otro verbo | `8408` |
| 9 | la nube sabe quién eres | `8409` |
| 10 | nombres internos | `8410` |
| 11 | entidades externas | `8411` |
| 12 | jefe final | `8412` |

> Igual que en http-headers, desde los contenedores tu máquina se ve como `host.docker.internal` (te hace falta en el nivel 6).

> Estado: los 12 niveles se probaron en Docker con un solver automático que los resuelve con la técnica prevista y valida el flag contra el hash del nivel (12/12).

## xss

15 niveles de XSS. Puerto `85` + el nivel con dos dígitos (nivel 1 → `8501`, nivel 15 → `8515`).

```bash
./lab.sh up xss 1
```

La víctima es un admin de verdad: un Chromium headless (el **bot**) que entra al nivel con su sesión iniciada cuando se lo pides desde **Reportar al admin**. El flag está en `/admin.php`, que solo ve el admin. Tu trabajo es que tu XSS se ejecute en su navegador y conseguir lo que ve (robando su sesión, o leyendo la página por él cuando la cookie es HttpOnly).

La primera vez que levantas un nivel de xss se construye el bot, que descarga Chromium (~1 GB). Después arranca solo con cada nivel.

Para recibir lo que exfiltres tienes el **servidor de exploits** en `http://127.0.0.1:8290`:

- Guardas páginas o scripts y quedan servidos en `http://exploit.labs.test/p/<nombre>` (así los ven el bot y los niveles).
- Cualquier otra petición a `http://exploit.labs.test/...` responde `ok` y queda en el registro (`http://127.0.0.1:8290/log`): ahí ves lo que te mande el navegador del admin.

Ojo con las direcciones: el admin ve el nivel como `http://xss-NN.labs.test`, no como `127.0.0.1`. En Reportar al admin mandas una ruta del sitio (`/?q=...`) o una página del servidor de exploits.

| Nivel | Pista | Puerto |
|---|---|---|
| 1 | sin filtros | `8501` |
| 2 | se queda guardado | `8502` |
| 3 | dentro de un atributo | `8503` |
| 4 | dentro de un script | `8504` |
| 5 | sin la etiqueta script | `8505` |
| 6 | entidades HTML | `8506` |
| 7 | nunca llega al servidor | `8507` |
| 8 | mensajes entre ventanas | `8508` |
| 9 | la política confía en ti misma | `8509` |
| 10 | rutas relativas | `8510` |
| 11 | la galleta no se deja tocar | `8511` |
| 12 | pocas letras | `8512` |
| 13 | un enlace que ejecuta | `8513` |
| 14 | decodificado de más | `8514` |
| 15 | jefe final | `8515` |

> Estado: los 15 niveles construyen y levantan en Docker, el bot entra con la sesión del admin cuando se le avisa y `/admin.php` niega el acceso a cualquier otro. Lo que **no** se ha probado es que cada nivel se resuelva con su técnica. Si uno no se comporta como dice su pista, abre un issue.

## csrf

12 niveles de CSRF. Puerto `86` + el nivel con dos dígitos (nivel 1 → `8601`, nivel 12 → `8612`).

```bash
./lab.sh up csrf 1
```

Tú entras como `carlos` / `carlos123`. El admin es el mismo bot de xss: tiene su sesión iniciada y abre lo que le mandes desde **Enviar enlace al admin**. Solo el admin puede cambiar roles; la portada de cada nivel te enseña el formulario (o la API) que usa para hacerlo y qué protección tiene. Tu objetivo es armar una página en el servidor de exploits que haga que el admin, sin saberlo, le dé a carlos el rol `admin`. Después entras como carlos a `/panel.php` y ahí está el flag.

El servidor de exploits (`http://127.0.0.1:8290`) está en `exploit.labs.test` y los niveles en `csrf-NN.labs.test`: distinto origen, pero el mismo sitio (`labs.test`). Eso importa en algunos niveles.

| Nivel | Pista | Puerto |
|---|---|---|
| 1 | sin protección | `8601` |
| 2 | basta con un enlace | `8602` |
| 3 | si no viene, no se revisa | `8603` |
| 4 | un token es un token | `8604` |
| 5 | galleta contra galleta | `8605` |
| 6 | cambia el verbo | `8606` |
| 7 | el Referer delata | `8607` |
| 8 | sin referencia | `8608` |
| 9 | no es un formulario... ¿o sí? | `8609` |
| 10 | mismo sitio, otro origen | `8610` |
| 11 | pregunta y te lo dicen | `8611` |
| 12 | jefe final | `8612` |

> Estado: los 12 niveles construyen y levantan en Docker, el bot entra con la sesión del admin, y el camino legítimo (el admin cambiando un rol con su sesión y, donde aplica, su token) funciona y hace que carlos vea el flag en el panel. Lo que **no** se ha probado es que cada nivel se resuelva con su técnica de CSRF. Si uno no se comporta como dice su pista, abre un issue.

## web

Aplicaciones completas, no niveles de una sola técnica. Puerto `87` + el nivel con dos dígitos (web 1 → `8701`).

```bash
./lab.sh up web 1
```

Cada web parece una app normal (una tienda, una mesa de ayuda, un foro) y esconde varias fallas de las categorías anteriores. Ninguna te da el flag sola: tienes que encontrarlas y encadenarlas, como en una máquina, hasta ejecutar comandos. El flag está en `/flag_XXXX.txt` con nombre aleatorio. No se dice qué técnicas lleva cada una; la pista va en el título de la página.

| Web | App | Pista | Puerto |
|---|---|---|---|
| 1 | Papelería Colón (tienda) | las cuentas de los demás | `8701` |
| 2 | SoporteYa (mesa de ayuda) | la oficina confía en lo que le dices | `8702` |
| 3 | Foro Quisqueya | el moderador lo lee todo | `8703` |

> La web 3 usa el bot de xss/csrf como víctima y el servidor de exploits (`http://127.0.0.1:8290`); lo levanta sola. Si en una web dañas algo que necesitas (por ejemplo, rompes un archivo con código mal escrito), `./lab.sh reset web <n>` la deja como nueva.

> Estado: las 3 webs se probaron en Docker con un solver automático que recorre la cadena prevista y valida el flag contra el hash del nivel (3/3).

## Panel de flags

```bash
./lab.sh panel
```

Abre `http://127.0.0.1:8200`, pega un flag y el panel te dice si es el del nivel. Valida **lfi**, **http-headers**, **idor**, **ssrf**, **xss**, **csrf** y **web**. Nunca conoce los flags: cada nivel, al arrancar, deja el SHA-256 de su flag en `.flags/<categoría>/<NN>` y el panel compara contra eso. Si reinicias un nivel el flag cambia, así que un flag viejo deja de valer.

El progreso (qué resolviste y cuándo) se guarda en `.flags/panel/progreso.json`. Cada categoría tiene un botón para reiniciar su progreso.
