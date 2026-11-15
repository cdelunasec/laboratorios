<?php
require '/var/www/lab/lab.php';
require '/var/www/html/_cors.php';

cors();
header('Content-Type: application/json; charset=utf-8');

$q = quien();
if ($q === null) {
    http_response_code(401);
    echo json_encode(['error' => 'Sin sesión']), "\n";
    exit;
}
echo json_encode(['usuario' => $q, 'token' => csrf_token($q)]), "\n";
