<?php
require '/var/www/lab/lab.php';

$me = require_login_api();

// v2: por uuid y con control de dueño. Reemplaza a la v1.
header('Deprecation: false');
header('Link: </api/v1/documentos.php>; rel="predecessor-version"');

if (($_GET['usuario'] ?? '') !== $me['uuid']) {
    json_response(['error' => 'Solo puedes ver tus propios documentos.'], 403);
}

$rows = query('SELECT d.id, d.titulo, d.contenido FROM documentos d WHERE d.user_id = ? ORDER BY d.id', [$me['id']])->fetchAll();
json_response($rows);
