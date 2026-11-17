#!/usr/bin/env bash
# Levanta, tumba y reinicia los niveles de los laboratorios.
# Uso: ./lab.sh <comando> [categoría] [nivel]

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

declare -A CATEGORIES=(
    [upload]="file-upload"
    [file-upload]="file-upload"
    [lfi]="lfi"
    [http]="http-headers"
    [headers]="http-headers"
    [cabeceras]="http-headers"
    [http-headers]="http-headers"
    [idor]="idor"
    [ssrf]="ssrf"
    [xss]="xss"
    [csrf]="csrf"
    [web]="web"
    [ad]="ad"
)
declare -A PORT_PREFIX=(
    [file-upload]="80"
    [lfi]="81"
    [http-headers]="82"
    [idor]="83"
    [ssrf]="84"
    [xss]="85"
    [csrf]="86"
    [web]="87"
    [ad]="ad"
)
# Categorías sin puerto publicado: viven en su propia red de Docker con IP fija.
# Solo puede correr un nivel a la vez porque todos usan la misma subred.
declare -A FIXED_NET=(
    [ad]="10.13.37.10 (dc01.quisqueya.lab)"
)
BOT_NET="labs-bot"

if docker compose version >/dev/null 2>&1; then
    COMPOSE=(docker compose)
elif command -v docker-compose >/dev/null 2>&1; then
    COMPOSE=(docker-compose)
else
    echo "[!] No encuentro docker compose. Instálalo con: sudo apt install docker-compose-plugin" >&2
    exit 1
fi

usage() {
    cat <<EOF
Uso: ./lab.sh <comando> [categoría] [nivel]

Comandos:
  up <cat> <n>      Construye y levanta el nivel
  down <cat> <n>    Apaga el nivel
  reset <cat> <n>   Lo borra y lo levanta limpio (archivos subidos fuera, flag nuevo)
  logs <cat> <n>    Muestra los logs del servidor
  list              Lista las categorías y niveles disponibles
  status            Muestra los niveles que están corriendo
  stop-all          Apaga todos los niveles (el panel y el bot quedan aparte)
  panel [up|down]   Levanta o apaga el panel de flags en http://127.0.0.1:8200
  bot [up|down]     Levanta o apaga el bot víctima y el servidor de exploits
                    (http://127.0.0.1:8290). Los niveles que lo usan lo levantan solos.

Categorías: upload (file-upload), lfi, http (http-headers), idor, ssrf, xss, csrf, web, ad

Ejemplo:
  ./lab.sh up upload 1      ->  http://127.0.0.1:8001
EOF
}

resolve() {
    local cat="${1:-}" lvl="${2:-}"
    [[ -n "$cat" && -n "$lvl" ]] || { usage; exit 1; }
    DIR_CAT="${CATEGORIES[$cat]:-}"
    [[ -n "$DIR_CAT" ]] || { echo "[!] Categoría desconocida: $cat" >&2; exit 1; }
    [[ "$lvl" =~ ^[0-9]+$ ]] || { echo "[!] El nivel tiene que ser un número" >&2; exit 1; }
    LVL=$(printf "%02d" "$((10#$lvl))")
    LEVEL_DIR="$ROOT/$DIR_CAT/level$LVL"
    [[ -d "$LEVEL_DIR" ]] || { echo "[!] No existe el nivel $LVL de $DIR_CAT" >&2; exit 1; }
    PROJECT="$DIR_CAT-$LVL"
    if [[ -n "${FIXED_NET[$DIR_CAT]:-}" ]]; then
        URL="${FIXED_NET[$DIR_CAT]}"
    else
        URL="http://127.0.0.1:${PORT_PREFIX[$DIR_CAT]}$LVL"
    fi
}

dc() {
    "${COMPOSE[@]}" -p "$PROJECT" -f "$LEVEL_DIR/docker-compose.yml" "$@"
}

bot_dc() {
    "${COMPOSE[@]}" -p labs-bot -f "$ROOT/bot/docker-compose.yml" "$@"
}

# Red compartida entre el bot, el servidor de exploits y los niveles de xss.
ensure_bot_net() {
    docker network inspect "$BOT_NET" >/dev/null 2>&1 || docker network create "$BOT_NET" >/dev/null
}

# Si el nivel necesita al bot (su compose usa la red labs-bot), lo levanta.
# La primera vez descarga Chromium.
ensure_bot() {
    grep -q "name: $BOT_NET" "$LEVEL_DIR/docker-compose.yml" || return 0
    ensure_bot_net
    if [[ -z "$(docker ps -q --filter name=^labs-bot$)" || -z "$(docker ps -q --filter name=^labs-exploit$)" ]]; then
        echo "[*] Levantando el bot víctima y el servidor de exploits..."
        bot_dc up -d --build
    fi
}

# Los niveles que validan con el panel escriben el hash de su flag al arrancar.
# Espera a que aparezca el hash nuevo para no anunciar el nivel antes de tiempo.
wait_hash() {
    local since="$1" f="$ROOT/.flags/$DIR_CAT/$LVL" tries=60
    grep -q lab-hashes "$LEVEL_DIR/docker-compose.yml" || return 0
    # Un dominio de AD se provisiona en el primer arranque: tarda más.
    [[ -n "${FIXED_NET[$DIR_CAT]:-}" ]] && tries=360
    for _ in $(seq 1 "$tries"); do
        if [[ -f "$f" ]] && (( $(stat -c %Y "$f" 2>/dev/null || echo 0) >= since )); then
            return 0
        fi
        sleep 0.5
    done
    echo "[!] El nivel no publicó su hash en $((tries / 2)) s. Revisa: ./lab.sh logs $DIR_CAT $((10#$LVL))" >&2
}

# En las categorías con red fija, apaga cualquier otro nivel de la misma
# categoría: chocarían en la subred.
free_fixed_net() {
    [[ -n "${FIXED_NET[$DIR_CAT]:-}" ]] || return 0
    local p
    for p in $(docker ps -a --format '{{.Label "com.docker.compose.project"}}' | grep -E "^$DIR_CAT-[0-9]+$" | sort -u); do
        [[ "$p" == "$PROJECT" ]] && continue
        echo "[*] Apagando $p (solo puede correr un nivel de $DIR_CAT a la vez)..."
        "${COMPOSE[@]}" -p "$p" -f "$ROOT/$DIR_CAT/level${p##*-}/docker-compose.yml" down >/dev/null 2>&1 || true
    done
}

# Contenedores o redes de todos los niveles (nunca el panel ni el bot).
level_ids() {
    local kind="$1" cat
    for cat in "${!PORT_PREFIX[@]}"; do
        if [[ "$kind" == net ]]; then
            docker network ls -q --filter "name=^$cat-"
        else
            docker ps -q --filter "name=^$cat-"
        fi
    done
}

cmd="${1:-}"
shift || true

case "$cmd" in
    up)
        resolve "$@"
        mkdir -p "$ROOT/.flags/$DIR_CAT"
        ensure_bot
        free_fixed_net
        since=$(date +%s)
        dc up -d --build
        wait_hash "$since"
        echo
        echo "[+] $DIR_CAT nivel $LVL listo en $URL"
        ;;
    down)
        resolve "$@"
        dc down
        ;;
    reset)
        resolve "$@"
        mkdir -p "$ROOT/.flags/$DIR_CAT"
        ensure_bot
        free_fixed_net
        dc down -v
        since=$(date +%s)
        dc up -d --build --force-recreate
        wait_hash "$since"
        echo
        echo "[+] $DIR_CAT nivel $LVL reiniciado en $URL"
        ;;
    logs)
        resolve "$@"
        dc logs -f
        ;;
    list)
        for dir in "$ROOT"/*/; do
            name="$(basename "$dir")"
            [[ -n "${PORT_PREFIX[$name]:-}" ]] || continue
            count=$(find "$dir" -maxdepth 1 -type d -name 'level*' | wc -l)
            if [[ -n "${FIXED_NET[$name]:-}" ]]; then
                echo "$name: $count niveles (uno a la vez, en ${FIXED_NET[$name]})"
            else
                echo "$name: $count niveles (puertos ${PORT_PREFIX[$name]}01-${PORT_PREFIX[$name]}$(printf "%02d" "$count"))"
            fi
        done
        ;;
    status)
        pattern="^($(IFS='|'; echo "${!PORT_PREFIX[*]}")|panel|labs-bot|labs-exploit)"
        docker ps --filter "label=com.docker.compose.project" --format '{{.Names}}\t{{.Ports}}' | grep -E "$pattern" || echo "No hay niveles corriendo."
        ;;
    stop-all)
        ids=$(level_ids ps)
        [[ -n "$ids" ]] && docker rm -f $ids >/dev/null
        # Borrar también las redes de cada nivel: si se quedan, Docker se
        # queda sin rangos de subred y los niveles nuevos no levantan.
        nets=$(level_ids net)
        [[ -n "$nets" ]] && docker network rm $nets >/dev/null 2>&1 || true
        echo "[+] Todos los niveles apagados."
        ;;
    panel)
        PANEL=("${COMPOSE[@]}" -p panel -f "$ROOT/panel/docker-compose.yml")
        case "${1:-up}" in
            up)
                mkdir -p "$ROOT/.flags/panel"
                for cat in "${!PORT_PREFIX[@]}"; do mkdir -p "$ROOT/.flags/$cat"; done
                "${PANEL[@]}" up -d --build
                echo
                echo "[+] Panel de flags listo en http://127.0.0.1:8200"
                ;;
            down)
                "${PANEL[@]}" down
                ;;
            *)
                usage
                exit 1
                ;;
        esac
        ;;
    bot)
        case "${1:-up}" in
            up)
                ensure_bot_net
                bot_dc up -d --build
                echo
                echo "[+] Bot listo. Servidor de exploits en http://127.0.0.1:8290"
                ;;
            down)
                bot_dc down
                ;;
            *)
                usage
                exit 1
                ;;
        esac
        ;;
    *)
        usage
        ;;
esac
