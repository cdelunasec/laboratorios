<?php
require '/var/www/lab/lab.php';

// CORS "para que funcione desde la app móvil y cualquier subdominio":
// refleja el Origin que llegue y permite credenciales.
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Credentials: true');
    header('Vary: Origin');
}
header('Content-Type: application/json; charset=utf-8');

$q = quien();
if ($q === null) {
    http_response_code(401);
    echo json_encode(['error' => 'Sin sesión']), "\n";
    exit;
}
echo json_encode(['usuario' => $q, 'token' => csrf_token($q)]), "\n";
