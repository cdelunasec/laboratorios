<?php
// Plantilla común del lab de cabeceras HTTP (local y controlado): cabecera,
// pie, mensajes y utilidades que comparten los niveles.

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function lab_header(string $level, string $app, string $tagline, string $hint): void
{
    $level = e($level);
    $app = e($app);
    $tagline = e($tagline);
    $hint = e($hint);
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
  <span class="tag">ssrf</span>
  <span class="tag level">nivel {$level}</span>
</header>
<main class="wrap">
  <section class="app">
    <p class="hint">{$hint}</p>
    <h1>{$app}</h1>
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

function lab_footer(string $goal): void
{
    $goal = e($goal);
    echo <<<HTML
  </section>
  <p class="goal">Objetivo: {$goal}</p>
</main>
</body>
</html>
HTML;
}

function lab_alert(string $type, string $text, ?string $link = null): void
{
    $html = e($text);
    if ($link !== null) {
        $html .= ' <a href="' . e($link) . '">' . e($link) . '</a>';
    }
    echo '<div class="alert ' . e($type) . '">' . $html . "</div>\n";
}

// Flag del nivel 3 (file://): está en /flag.txt.
function lab_flag(): string
{
    return trim((string) @file_get_contents('/flag.txt'));
}

// Respuesta JSON.
function lab_json($data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), "\n";
    exit;
}

// Pide una URL desde el servidor con cURL. Devuelve [código, cuerpo, error].
// $protocolos: los esquemas que cURL acepta (por defecto solo http/https).
function lab_curl(string $url, int $protocolos = CURLPROTO_HTTP | CURLPROTO_HTTPS, bool $seguir = false, string $metodo = 'GET'): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_PROTOCOLS => $protocolos,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        CURLOPT_FOLLOWLOCATION => $seguir,
        CURLOPT_MAXREDIRS => 5,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT => 6,
        CURLOPT_USERAGENT => 'VistaPrevia/1.0',
        CURLOPT_CUSTOMREQUEST => $metodo,
    ]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    return [$code, $body === false ? '' : (string) $body, $err];
}

// Formulario estándar de "URL" que usan casi todos los niveles.
function lab_url_form(string $label = 'URL', string $placeholder = 'https://ejemplo.com/', string $valor = ''): void
{
    echo '    <form class="stack" method="post">' . "\n";
    echo '      <label>' . e($label) . "</label>\n";
    echo '      <input type="text" name="url" placeholder="' . e($placeholder) . '" value="' . e($valor) . '">' . "\n";
    echo "      <button>Enviar</button>\n    </form>\n";
}
