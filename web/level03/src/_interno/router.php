<?php
// Panel de diagnóstico del servidor del foro (lab local controlado).
// Solo escucha en 127.0.0.1:8080: desde fuera no se ve.

header('Content-Type: text/plain; charset=utf-8');
$ruta = (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($ruta) {
    case '/':
        echo "Diagnóstico del servidor\n\n";
        echo "  GET /estado            carga y memoria\n";
        echo "  GET /dns?host=<nombre> resuelve un nombre (getent)\n";
        break;

    case '/estado':
        echo 'Carga: ' . implode(' ', array_map(fn ($x) => round($x, 2), sys_getloadavg())) . "\n";
        echo 'Memoria PHP: ' . round(memory_get_usage() / 1024) . " KB\n";
        echo 'Hora: ' . date('c') . "\n";
        break;

    case '/dns':
        $host = (string) ($_GET['host'] ?? '');
        if ($host === '') {
            http_response_code(400);
            echo "Falta ?host=\n";
            break;
        }
        // Nada de encadenar comandos.
        if (preg_match('/[;&]/', $host)) {
            http_response_code(400);
            echo "Caracteres no permitidos\n";
            break;
        }
        echo shell_exec('getent hosts ' . $host . ' 2>&1') ?: "Sin resultado para $host\n";
        break;

    default:
        http_response_code(404);
        echo "No existe\n";
}
