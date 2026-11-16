<?php
// Utilidades comunes del lab de aplicaciones web completas (local y
// controlado). Cada nivel define en /var/www/lib/app.php:
//   LAB_APP   nombre de la aplicación
//   LAB_HINT  pista del nivel (va en el título de la página)
//   LAB_EXTRA texto extra para el pie (cuentas, servidor de exploits...)
// y su propia navegación con lab_header($tagline, $menu, $sesion).

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

// $menu: [href => texto]. $sesion: HTML ya escapado para la esquina de la barra.
function lab_header(string $tagline, array $menu = [], string $sesion = ''): void
{
    $level = e(getenv('LAB_LEVEL') ?: '');
    $app = e(LAB_APP);
    $hint = e(LAB_HINT);
    $tagline = e($tagline);
    $nav = '';
    foreach ($menu as $href => $txt) {
        $nav .= '<a href="' . e($href) . '">' . e($txt) . '</a>';
    }
    echo <<<HTML
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$app} · Nivel {$level} · {$hint}</title>
<link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header class="lab-bar">
  <span class="brand">cdelunasec<span class="dim">/labs</span></span>
  <span class="tag">web</span>
  <span class="tag level">nivel {$level}</span>
  <span class="session">{$sesion}</span>
</header>
<main class="wrap">
  <section class="app">
    <p class="hint">{$hint}</p>
    <h1><a href="/" class="home">{$app}</a></h1>
    <p class="tagline">{$tagline}</p>
    <nav class="menu">{$nav}</nav>

HTML;
}

function lab_footer(): void
{
    $goal = e(defined('LAB_OBJETIVO') ? LAB_OBJETIVO : LAB_GOAL);
    $extra = LAB_EXTRA !== '' ? e(LAB_EXTRA) . ' · ' : '';
    echo <<<HTML
  </section>
  <p class="goal">{$extra}Objetivo: {$goal}</p>
</main>
</body>
</html>
HTML;
}

function lab_alert(string $type, string $text): void
{
    echo '<div class="alert ' . e($type) . '">' . e($text) . "</div>\n";
}
