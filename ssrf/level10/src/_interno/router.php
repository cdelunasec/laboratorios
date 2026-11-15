<?php
// Servicio interno de administración (lab local). Solo escucha en 127.0.0.1.
$flag = trim((string) file_get_contents('/var/lib/interno/flag'));
$ruta = (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
header('Content-Type: text/plain; charset=utf-8');

switch ($ruta) {
    case '/':
        echo "Panel interno de administración\n\nRutas:\n  /estado\n  /secreto\n";
        break;
    case '/estado':
        echo "ok\n";
        break;
    case '/secreto':
        echo "Clave interna: $flag\n";
        break;
    default:
        http_response_code(404);
        echo "No encontrado\n";
}
