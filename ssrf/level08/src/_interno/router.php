<?php
// Panel interno (lab local). Solo escucha en 127.0.0.1.
$flag = trim((string) file_get_contents('/var/lib/interno/flag'));
$ruta = (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
header('Content-Type: text/plain; charset=utf-8');

switch ($ruta) {
    case '/':
        echo "Panel interno\n\nRutas:\n  GET  /estado\n  POST /admin/exportar   (exporta la configuración)\n";
        break;
    case '/estado':
        echo "ok\n";
        break;
    case '/admin/exportar':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            echo "Método no permitido: usa POST.\n";
            break;
        }
        echo "Exportación de configuración\nclave_maestra=$flag\n";
        break;
    default:
        http_response_code(404);
        echo "No encontrado\n";
}
