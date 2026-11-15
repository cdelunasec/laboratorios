<?php
require '/var/www/lab/lab.php';

lab_header('07', 'Noticias del Barrio', 'Lo que pasa en la comunidad, al momento.', 'el filtro mira la URL cruda');
lab_nav(['?file=portada.php' => 'Portada', '?file=deportes.php' => 'Deportes']);

$raw = $_SERVER['QUERY_STRING'] ?? '';

// "WAF" casero: si ve .. en la URL, bloquea.
if (str_contains($raw, '..')) {
    lab_alert('err', 'Ataque detectado: path traversal.');
} else {
    include 'pages/' . ($_GET['file'] ?? 'portada.php');
}

lab_footer('lee el archivo /flag.txt del servidor.');
