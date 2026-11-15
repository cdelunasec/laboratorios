<?php
// Plantilla común de los niveles de IDOR (lab local y controlado): base de
// datos, sesión, seed y vistas. Cada nivel define en nivel.php LAB_APP,
// LAB_HINT y LAB_GOAL.

require __DIR__ . '/nivel.php';

if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start();
}

const LAB_DB = '/var/lab/app.db';

function e($s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO('sqlite:' . LAB_DB);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA busy_timeout = 3000');
    }
    return $pdo;
}

function query(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

// --- Seed (solo lo usa seed.php, como root, en el primer arranque) ---

function read_flag(): string
{
    return trim((string) file_get_contents('/var/lib/lab/flag'));
}

function uuid4(): string
{
    $b = random_bytes(16);
    $b[6] = chr((ord($b[6]) & 0x0f) | 0x40);
    $b[8] = chr((ord($b[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($b), 4));
}

// Usuarios comunes a todos los niveles. Tu cuenta es carlos (id 4).
// $notas_admin va en las notas privadas del admin (muchos niveles esconden ahí el flag).
function seed_users(string $notas_admin = 'Sin notas.'): void
{
    db()->exec('CREATE TABLE users (
        id INTEGER PRIMARY KEY,
        uuid TEXT UNIQUE,
        username TEXT UNIQUE,
        password TEXT,
        email TEXT,
        full_name TEXT,
        role TEXT DEFAULT "user",
        notas TEXT DEFAULT ""
    )');
    $users = [
        [1, 'admin', bin2hex(random_bytes(16)), 'admin@empresa.local', 'Administrador', 'admin', $notas_admin],
        [2, 'maria', bin2hex(random_bytes(16)), 'maria@empresa.local', 'María Pérez', 'user', 'Recordar renovar el pasaporte.'],
        [3, 'juan', bin2hex(random_bytes(16)), 'juan@empresa.local', 'Juan Rodríguez', 'user', 'Cumpleaños de mami el 12.'],
        [4, 'carlos', 'carlos123', 'carlos@empresa.local', 'Carlos Gómez', 'user', 'Mis notas: comprar café.'],
    ];
    foreach ($users as [$id, $user, $pass, $mail, $name, $role, $notas]) {
        query('INSERT INTO users (id, uuid, username, password, email, full_name, role, notas) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$id, uuid4(), $user, password_hash($pass, PASSWORD_DEFAULT), $mail, $name, $role, $notas]);
    }
}

// --- Sesión ---

function current_user(): ?array
{
    if (empty($_SESSION['uid'])) {
        return null;
    }
    return query('SELECT * FROM users WHERE id = ?', [$_SESSION['uid']])->fetch() ?: null;
}

function require_login(): array
{
    $user = current_user();
    if ($user === null) {
        header('Location: /login.php');
        exit;
    }
    return $user;
}

function require_login_api(): array
{
    $user = current_user();
    if ($user === null) {
        json_response(['error' => 'No autenticado'], 401);
    }
    return $user;
}

function json_response($data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), "\n";
    exit;
}

// --- Vistas ---

function lab_header(string $tagline): void
{
    $user = current_user();
    $session = $user
        ? '<span class="dim">' . e($user['username']) . '</span> <a href="/logout.php">Salir</a>'
        : '<a href="/login.php">Entrar</a>';
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
  <span class="tag">idor</span>
  <span class="tag level">nivel {$level}</span>
  <span class="session">{$session}</span>
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
    echo "</nav>\n";
}

function lab_footer(): void
{
    $goal = e(LAB_GOAL);
    echo <<<HTML
  </section>
  <p class="goal">Tu cuenta: carlos / carlos123 · Objetivo: {$goal}</p>
</main>
</body>
</html>
HTML;
}

function lab_alert(string $type, string $text): void
{
    echo '<div class="alert ' . e($type) . '">' . e($text) . "</div>\n";
}
