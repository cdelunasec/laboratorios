<?php
// Servicio interno de licencias (lab local). Solo escucha en 127.0.0.1.
$flag = trim((string) file_get_contents('/var/lib/interno/flag'));
$ruta = (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
header('Content-Type: text/plain; charset=utf-8');

if ($ruta === '/verificar') {
    $prefijo = (string) ($_GET['prefijo'] ?? '');
    if ($prefijo !== '' && str_starts_with($flag, $prefijo)) {
        echo "valida\n";
    } else {
        http_response_code(404);
        echo "no\n";
    }
} else {
    http_response_code(404);
    echo "No encontrado\n";
}
