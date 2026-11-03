<?php
// Plantilla común: cabecera, pie y mensajes de cada nivel.

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function lab_header(string $level, string $app, string $tagline): void
{
    $level = e($level);
    $app = e($app);
    $tagline = e($tagline);
    echo <<<HTML
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$app} · Nivel {$level}</title>
<link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header class="lab-bar">
  <span class="brand">cdelunasec<span class="dim">/labs</span></span>
  <span class="tag">file-upload</span>
  <span class="tag level">nivel {$level}</span>
</header>
<main class="wrap">
  <section class="app">
    <h1>{$app}</h1>
    <p class="tagline">{$tagline}</p>

HTML;
}

function lab_footer(): void
{
    echo <<<HTML
  </section>
  <p class="goal">Objetivo: ejecuta comandos en el servidor y lee el archivo <code>/flag_*.txt</code>.</p>
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

function lab_upload_form(string $label = 'Selecciona un archivo', string $extra = ''): void
{
    $label = e($label);
    echo <<<HTML
    <form method="post" enctype="multipart/form-data" class="upload">
      <label for="file">{$label}</label>
      <input type="file" name="file" id="file" required{$extra}>
      <button type="submit">Subir</button>
    </form>

HTML;
}
