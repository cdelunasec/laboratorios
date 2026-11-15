<?php
// Plantilla común del lab de XSS (local y controlado). Cada nivel define en
// nivel.php: LAB_APP, LAB_HINT, LAB_GOAL, LAB_HTTPONLY (la cookie del admin es
// HttpOnly) y LAB_CLICK (selector en el que el admin hace clic, o null).

require __DIR__ . '/nivel.php';

const LAB_DATOS = '/var/lib/labapp/datos';
const LAB_BOT = 'http://bot.labs.test:3000/visit';
const LAB_COOKIE = 'admin_sesion';

function lab_host(): string
{
    return getenv('LAB_ID') . '.labs.test';
}

function e($s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function admin_token(): string
{
    return trim((string) @file_get_contents('/var/lib/labapp/admin_token'));
}

function es_admin(): bool
{
    $t = admin_token();
    return $t !== '' && hash_equals($t, (string) ($_COOKIE[LAB_COOKIE] ?? ''));
}

// Datos simples de la app en JSON (libro de visitas, perfiles, notas...).
function datos_leer(string $nombre): array
{
    return json_decode((string) @file_get_contents(LAB_DATOS . "/$nombre.json"), true) ?: [];
}

function datos_guardar(string $nombre, array $d): void
{
    file_put_contents(LAB_DATOS . "/$nombre.json", json_encode($d, JSON_UNESCAPED_UNICODE), LOCK_EX);
}

// Le pide al bot (el admin) que visite una URL con su sesión.
function bot_visitar(string $url): array
{
    $cookie = ['name' => LAB_COOKIE, 'value' => admin_token(), 'domain' => lab_host(), 'httpOnly' => LAB_HTTPONLY];
    $ch = curl_init(LAB_BOT);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['url' => $url, 'cookies' => [$cookie], 'click' => LAB_CLICK]),
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

function lab_header(string $tagline): void
{
    $level = e(getenv('LAB_LEVEL') ?: '');
    $app = e(LAB_APP);
    $hint = e(LAB_HINT);
    $tagline = e($tagline);
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
  <span class="tag">xss</span>
  <span class="tag level">nivel {$level}</span>
</header>
<main class="wrap">
  <section class="app">
    <p class="hint">{$hint}</p>
    <h1><a href="/" class="home">{$app}</a></h1>
    <p class="tagline">{$tagline}</p>

HTML;
}

function lab_nav(array $links): void
{
    echo '    <nav class="menu">';
    foreach ($links as $href => $label) {
        echo '<a href="' . e($href) . '">' . e($label) . '</a>';
    }
    echo '<a href="/reportar.php">Reportar al admin</a>';
    echo "</nav>\n";
}

function lab_footer(): void
{
    $goal = e(LAB_GOAL);
    echo <<<HTML
  </section>
  <p class="goal">Objetivo: {$goal} El flag está en /admin.php, que solo ve el admin. Servidor de exploits: http://127.0.0.1:8290</p>
</main>
</body>
</html>
HTML;
}

function lab_alert(string $type, string $text): void
{
    echo '<div class="alert ' . e($type) . '">' . e($text) . "</div>\n";
}
