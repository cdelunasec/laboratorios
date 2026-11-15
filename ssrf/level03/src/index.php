<?php
require '/var/www/lab/lab.php';

lab_header('03', 'Lector de Feeds', 'Agrega el feed RSS de tu blog favorito.', 'no solo http');

$url = trim((string) ($_POST['url'] ?? ''));
lab_url_form('URL del feed', 'https://blog.ejemplo.com/feed.xml', $url);

if ($url !== '') {
    // "Compatibilidad" con feeds guardados en disco por el equipo editorial.
    [$code, $body, $err] = lab_curl($url, CURLPROTO_HTTP | CURLPROTO_HTTPS | CURLPROTO_FILE);
    if ($err) {
        lab_alert('err', "Error: $err");
    } else {
        lab_alert('ok', 'Feed leído (' . strlen($body) . ' bytes).');
        echo '    <pre>' . e(substr($body, 0, 4000)) . "</pre>\n";
    }
}

lab_footer('lee el archivo /flag.txt del servidor.');
