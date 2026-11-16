<?php
// SoporteYa: mesa de ayuda (nivel 2 del lab de webs completas).
require __DIR__ . '/lab.php';

const LAB_APP = 'SoporteYa';
const LAB_HINT = 'la oficina confía en lo que le dices';
const LAB_EXTRA = '';
const LAB_TEMA = 'soporte';
const LAB_PIE = 'SoporteYa · soporte técnico para pymes';
const LAB_LOGO = '<svg viewBox="0 0 32 32" fill="none" aria-hidden="true"><rect x="2" y="2" width="28" height="28" rx="9" fill="#7aa2ff"/><path d="M9 17v-2a7 7 0 0 1 14 0v2" stroke="#08101f" stroke-width="2.4" stroke-linecap="round"/><rect x="7.5" y="16" width="4" height="6" rx="1.6" fill="#08101f"/><rect x="20.5" y="16" width="4" height="6" rx="1.6" fill="#08101f"/><path d="M22.5 22c0 2-2 3-5 3" stroke="#08101f" stroke-width="2" stroke-linecap="round"/></svg>';

const SOPORTE = '/var/www/soporte';
const BITACORA = SOPORTE . '/logs/acceso.log';

// Bitácora de accesos propia de la app (Apache manda la suya a la consola).
function registrar_acceso(): void
{
    $linea = sprintf(
        "[%s] %s \"%s %s\" \"%s\"\n",
        date('d/M/Y:H:i:s O'),
        $_SERVER['REMOTE_ADDR'] ?? '-',
        $_SERVER['REQUEST_METHOD'] ?? '-',
        $_SERVER['REQUEST_URI'] ?? '-',
        $_SERVER['HTTP_USER_AGENT'] ?? '-'
    );
    @file_put_contents(BITACORA, $linea, FILE_APPEND | LOCK_EX);
}

// IP del cliente. Detrás del balanceador la real viene en X-Forwarded-For.
function ip_cliente(): string
{
    $xff = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    if ($xff !== '') {
        return trim(explode(',', $xff)[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '';
}

function desde_oficina(): bool
{
    $ip = ip_cliente();
    return $ip === '127.0.0.1' || str_starts_with($ip, '10.');
}

function tickets(): array
{
    $t = glob(SOPORTE . '/tickets/*.txt') ?: [];
    sort($t);
    return array_map('basename', $t);
}

// Lee las cabeceras de un ticket (Cliente, Asunto, Estado...) para la tabla interna.
function datos_ticket(string $archivo): array
{
    $d = [];
    foreach (array_slice(file(SOPORTE . "/tickets/$archivo", FILE_IGNORE_NEW_LINES) ?: [], 0, 6) as $l) {
        if (preg_match('/^(\w+): (.*)$/u', $l, $m)) {
            $d[$m[1]] = $m[2];
        }
    }
    return $d;
}

function cabecera(string $titulo, string $sub = '', bool $estrecha = false): void
{
    registrar_acceso();
    $sesion = desde_oficina() && str_starts_with((string) ($_SERVER['REQUEST_URI'] ?? ''), '/interno/')
        ? '<span class="chip"><span class="punto"></span>agente · oficina</span>'
        : '<a href="/interno/" class="muted chica" style="color: var(--faint)">Acceso agentes</a>';
    lab_header($titulo, ['/' => 'Inicio', '/nuevo.php' => 'Abrir ticket', '/ticket.php' => 'Consultar ticket'], $sesion, ['sub' => $sub, 'estrecha' => $estrecha]);
}

function exigir_oficina(): void
{
    if (desde_oficina()) {
        return;
    }
    http_response_code(403);
    cabecera('', '', true);
    echo '  <div class="caja bloqueo ld">';
    echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg>';
    echo '<h2>Acceso restringido</h2>';
    lab_alert('err', 'Acceso restringido a la red de la oficina (10.0.0.0/8). Tu IP: ' . ip_cliente());
    echo '<p class="muted chica">Si eres agente y estás fuera, conéctate a la VPN de la oficina.</p></div>' . "\n";
    lab_footer();
    exit;
}
