<?php
// Plantilla común del lab de CSRF (local y controlado). Cada nivel define en
// nivel.php: LAB_APP, LAB_HINT, LAB_SAMESITE (de la cookie del admin) y
// LAB_DOC (cómo es el formulario con el que el admin da roles).
//
// Tú eres carlos (carlos / carlos123). El admin es el bot compartido, que
// entra con su sesión cuando le mandas un enlace. Si consigues que el admin le
// dé a carlos el rol "admin", carlos ve el flag en /panel.php.

require __DIR__ . '/nivel.php';

if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start();
}

const LAB_DATOS = '/var/lib/labapp/datos';
const LAB_BOT = 'http://bot.labs.test:3000/visit';
const LAB_COOKIE = 'admin_sesion';
const LAB_GOAL = 'consigue que el admin le dé a carlos el rol "admin". Luego entra como carlos a /panel.php.';

function lab_host(): string
{
    return getenv('LAB_ID') . '.labs.test';
}

function e($s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function datos_leer(string $nombre): array
{
    return json_decode((string) @file_get_contents(LAB_DATOS . "/$nombre.json"), true) ?: [];
}

function datos_guardar(string $nombre, array $d): void
{
    file_put_contents(LAB_DATOS . "/$nombre.json", json_encode($d, JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function usuarios(): array
{
    $u = datos_leer('usuarios');
    if (!$u) {
        $u = [
            'admin' => ['rol' => 'admin'],
            'carlos' => ['clave' => password_hash('carlos123', PASSWORD_DEFAULT), 'rol' => 'usuario'],
            'maria' => ['clave' => password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT), 'rol' => 'usuario'],
        ];
        datos_guardar('usuarios', $u);
    }
    return $u;
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

// Quién hace la petición: 'admin' (cookie del admin), el usuario de la sesión, o null.
function quien(): ?string
{
    if (es_admin()) {
        return 'admin';
    }
    return $_SESSION['usuario'] ?? null;
}

function rol_de(?string $u): ?string
{
    return $u === null ? null : (usuarios()[$u]['rol'] ?? null);
}

// Tokens anti-CSRF por usuario (los niveles que los usan deciden cómo los validan).
function csrf_token(string $u): string
{
    $t = datos_leer('tokens');
    if (empty($t[$u])) {
        $t[$u] = bin2hex(random_bytes(16));
        datos_guardar('tokens', $t);
    }
    return $t[$u];
}

// Lo que hace el admin: cambiar el rol de un usuario.
function cambiar_rol(string $usuario, string $rol): bool
{
    $u = usuarios();
    if (!isset($u[$usuario]) || $usuario === 'admin' || !in_array($rol, ['usuario', 'editor', 'admin'], true)) {
        return false;
    }
    $u[$usuario]['rol'] = $rol;
    datos_guardar('usuarios', $u);
    return true;
}

function exigir_admin(): void
{
    if (!es_admin()) {
        http_response_code(403);
        header('Content-Type: text/plain; charset=utf-8');
        echo "Solo el admin puede cambiar roles.\n";
        exit;
    }
}

function responder(bool $ok, string $msg): void
{
    http_response_code($ok ? 200 : 400);
    header('Content-Type: text/plain; charset=utf-8');
    echo $msg, "\n";
    exit;
}

// Le pide al bot (el admin) que visite una URL con su sesión.
function bot_visitar(string $url): array
{
    $cookie = ['name' => LAB_COOKIE, 'value' => admin_token(), 'domain' => lab_host(), 'httpOnly' => true, 'sameSite' => LAB_SAMESITE];
    $ch = curl_init(LAB_BOT);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['url' => $url, 'cookies' => [$cookie]]),
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
    $q = quien();
    $sesion = $q ? '<span class="dim">' . e($q) . '</span> <a href="/logout.php">Salir</a>' : '<a href="/login.php">Entrar</a>';
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
  <span class="tag">csrf</span>
  <span class="tag level">nivel {$level}</span>
  <span class="session">{$sesion}</span>
</header>
<main class="wrap">
  <section class="app">
    <p class="hint">{$hint}</p>
    <h1><a href="/" class="home">{$app}</a></h1>
    <p class="tagline">{$tagline}</p>
    <nav class="menu"><a href="/">Inicio</a><a href="/panel.php">Panel</a><a href="/reportar.php">Enviar enlace al admin</a></nav>

HTML;
}

function lab_footer(): void
{
    $goal = e(LAB_GOAL);
    echo <<<HTML
  </section>
  <p class="goal">Tu cuenta: carlos / carlos123 · Objetivo: {$goal} Servidor de exploits: http://127.0.0.1:8290</p>
</main>
</body>
</html>
HTML;
}

function lab_alert(string $type, string $text): void
{
    echo '<div class="alert ' . e($type) . '">' . e($text) . "</div>\n";
}
