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

Categorías disponibles: `upload` (alias de `file-upload`) y `lfi`.

## El objetivo

Cada nivel esconde un archivo `/flag_XXXX.txt` con un nombre aleatorio. No lo puedes adivinar: tienes que conseguir **ejecución de comandos** en el servidor, listar el sistema de archivos y leerlo. El flag tiene el formato `cdelunasec{...}`.

Cada nivel corre en su propio contenedor aislado: lo que hagas en uno no afecta a los demás.

## Categorías

- [x] **file-upload** — 20 niveles. Subir un archivo arbitrario hasta lograr RCE, burlando defensas cada vez más fuertes.
- [x] **lfi** — 20 niveles. Local File Inclusion, de leer un archivo directo a encadenar LFI con RCE.
- [ ] http / cabeceras
- [ ] xss
- [ ] ssrf
- [ ] csrf
- [ ] idor
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
