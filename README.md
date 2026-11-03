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

Categoría disponible: `upload` (alias de `file-upload`).

## El objetivo

Cada nivel esconde un archivo `/flag_XXXX.txt` con un nombre aleatorio. No lo puedes adivinar: tienes que conseguir **ejecución de comandos** en el servidor, listar el sistema de archivos y leerlo. El flag tiene el formato `cdelunasec{...}`.

Cada nivel corre en su propio contenedor aislado: lo que hagas en uno no afecta a los demás.

## Categorías

- [x] **file-upload** — 20 niveles. Subir un archivo arbitrario hasta lograr RCE, burlando defensas cada vez más fuertes.
- [ ] lfi
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
