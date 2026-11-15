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
)
declare -A PORT_PREFIX=(
    [file-upload]="80"
    [lfi]="81"
    [http-headers]="82"
)

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
  stop-all          Apaga todos los niveles (el panel queda aparte)
  panel [up|down]   Levanta o apaga el panel de flags en http://127.0.0.1:8200

Categorías: upload (file-upload), lfi, http (http-headers)

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
    URL="http://127.0.0.1:${PORT_PREFIX[$DIR_CAT]}$LVL"
}

dc() {
    "${COMPOSE[@]}" -p "$PROJECT" -f "$LEVEL_DIR/docker-compose.yml" "$@"
}

cmd="${1:-}"
shift || true

case "$cmd" in
    up)
        resolve "$@"
        mkdir -p "$ROOT/.flags/$DIR_CAT"
        dc up -d --build
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
        dc down -v
        dc up -d --build --force-recreate
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
            echo "$name: $count niveles (puertos ${PORT_PREFIX[$name]}01-${PORT_PREFIX[$name]}$(printf "%02d" "$count"))"
        done
        ;;
    status)
        docker ps --filter "label=com.docker.compose.project" --format '{{.Names}}\t{{.Ports}}' | grep -E '^(file-upload|lfi|http-headers|panel)' || echo "No hay niveles corriendo."
        ;;
    stop-all)
        ids=$(docker ps -q --filter "name=^file-upload-"; docker ps -q --filter "name=^lfi-"; docker ps -q --filter "name=^http-headers-")
        [[ -n "$ids" ]] && docker rm -f $ids >/dev/null
        # Borrar también las redes de cada nivel: si se quedan, Docker se
        # queda sin rangos de subred y los niveles nuevos no levantan.
        nets=$(docker network ls -q --filter "name=^file-upload-"; docker network ls -q --filter "name=^lfi-"; docker network ls -q --filter "name=^http-headers-")
        [[ -n "$nets" ]] && docker network rm $nets >/dev/null 2>&1 || true
        echo "[+] Todos los niveles apagados."
        ;;
    panel)
        PANEL=("${COMPOSE[@]}" -p panel -f "$ROOT/panel/docker-compose.yml")
        case "${1:-up}" in
            up)
                mkdir -p "$ROOT/.flags/panel" "$ROOT/.flags/lfi" "$ROOT/.flags/http-headers"
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
    *)
        usage
        ;;
esac
