<?php
// Plantilla común del lab de LFI (local y controlado): cabecera, pie y mensajes.

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
  <span class="tag">lfi</span>
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
    echo "</nav>\n    <div class=\"content\">\n";
}

function lab_footer(string $goal): void
{
    $goal = e($goal);
    echo <<<HTML
    </div>
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
