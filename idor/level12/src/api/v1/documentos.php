<?php
require '/var/www/lab/lab.php';

// v1 (obsoleta): lista los documentos de un dueño por id interno. Sin control
// de dueño y sin contenido; el contenido se pide aparte en documento.php.
require_login_api();

if (!isset($_GET['owner'])) {
    json_response(['error' => 'Falta el parámetro owner (id interno del usuario).'], 400);
}

$rows = query('SELECT id, titulo FROM documentos WHERE user_id = ? ORDER BY id', [$_GET['owner']])->fetchAll();
json_response(['owner' => (int) $_GET['owner'], 'documentos' => $rows, 'detalle' => '/api/v1/documento.php?id=<id>']);
