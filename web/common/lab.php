<?php
// Utilidades comunes del lab de aplicaciones web completas (local y
// controlado). Cada nivel define en /var/www/lib/app.php:
//   LAB_APP   nombre de la aplicación
//   LAB_HINT  pista del nivel (va en el título de la página y en la píldora)
//   LAB_EXTRA texto extra para la píldora (cuentas, servidor de exploits...)
//   LAB_TEMA  hoja de estilos propia de la app (/assets/<tema>.css)
//   LAB_LOGO  SVG del logo
//   LAB_PIE   texto del pie de página
// y su propia navegación con lab_header($titulo, $menu, $sesion, $opts).

if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start();
}

const LAB_DATOS = '/var/lib/labapp/datos';
const LAB_BOT = 'http://bot.labs.test:3000/visit';
const LAB_GOAL = 'ejecuta comandos en el servidor y lee el flag (/flag_XXXX.txt).';

function e($s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function lab_host(): string
{
    return getenv('LAB_ID') . '.labs.test';
}

function datos_leer(string $nombre): array
{
    return json_decode((string) @file_get_contents(LAB_DATOS . "/$nombre.json"), true) ?: [];
}

function datos_guardar(string $nombre, array $d): void
{
    file_put_contents(LAB_DATOS . "/$nombre.json", json_encode($d, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
}

function admin_token(): string
{
    return trim((string) @file_get_contents('/var/lib/labapp/admin_token'));
}

function lab_json($data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), "\n";
    exit;
}

function lab_redirect(string $a): void
{
    header('Location: ' . $a);
    exit;
}

// Le pide al bot (el admin/moderador del nivel) que visite una URL con su sesión.
function bot_visitar(string $url, array $cookies): array
{
    $ch = curl_init(LAB_BOT);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['url' => $url, 'cookies' => $cookies]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT => 40,
    ]);
    $r = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    return [$code === 200, $r === false ? 'el bot no responde' : (string) $r];
}

// Inicial para los avatares de letra.
function inicial(string $nombre): string
{
    return mb_strtoupper(mb_substr($nombre, 0, 1));
}

// $menu: [href => texto]. $sesion: HTML ya escapado para la esquina derecha.
// $opts: 'sub' (bajada del título), 'estrecha' (página angosta, para formularios).
// Si $titulo es '', la página pinta su propio encabezado (por ejemplo, un hero).
function lab_header(string $titulo, array $menu = [], string $sesion = '', array $opts = []): void
{
    $ruta = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $nav = '';
    foreach ($menu as $href => $txt) {
        $activo = $href === '/' ? in_array($ruta, ['/', '/index.php'], true)
            : ($ruta === $href || (str_ends_with($href, '/') && str_starts_with($ruta, $href)));
        $nav .= '<a href="' . e($href) . '"' . ($activo ? ' class="activo" aria-current="page"' : '') . '>' . e($txt) . '</a>';
    }
    $titulo_html = e($titulo);
    $level = e(getenv('LAB_LEVEL') ?: '');
    $app = e(LAB_APP);
    $hint = e(LAB_HINT);
    $tema = e(LAB_TEMA);
    $logo = LAB_LOGO;
    $clase = empty($opts['estrecha']) ? 'page' : 'page estrecha';
    $titulo_tab = $titulo !== '' ? "$titulo_html · " : '';
    echo <<<HTML
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$titulo_tab}{$app} · Nivel {$level} · {$hint}</title>
<link rel="stylesheet" href="/assets/base.css">
<link rel="stylesheet" href="/assets/{$tema}.css">
</head>
<body class="tema-{$tema}">
<header class="top">
  <div class="top-in">
    <a class="marca" href="/">{$logo}<span>{$app}</span></a>
    <nav class="nav">{$nav}</nav>
    <div class="sesion">{$sesion}</div>
  </div>
</header>
<main class="{$clase}">

HTML;
    if ($titulo !== '') {
        $sub = isset($opts['sub']) && $opts['sub'] !== '' ? '<p>' . e($opts['sub']) . '</p>' : '';
        echo "  <div class=\"page-head ld\"><h1>{$titulo_html}</h1>{$sub}</div>\n";
    }
}

function lab_footer(): void
{
    $level = e(getenv('LAB_LEVEL') ?: '');
    $hint = e(LAB_HINT);
    $goal = e(defined('LAB_OBJETIVO') ? LAB_OBJETIVO : LAB_GOAL);
    $extra = LAB_EXTRA !== '' ? '<p><span>extra:</span> ' . e(LAB_EXTRA) . '</p>' : '';
    $pie = e(LAB_PIE);
    echo <<<HTML
</main>
<footer class="pie">{$pie}</footer>
<details class="lab-pill">
  <summary><span class="led"></span><b>web</b> · nivel {$level}</summary>
  <div class="info">
    <p><span>cdelunasec/labs</span></p>
    <p><span>pista:</span> {$hint}</p>
    <p><span>objetivo:</span> {$goal}</p>
    {$extra}
  </div>
</details>
<script src="/assets/base.js"></script>
</body>
</html>
HTML;
}

function lab_alert(string $type, string $text): void
{
    echo '<div class="aviso ' . e($type) . ' ld">' . e($text) . "</div>\n";
}
