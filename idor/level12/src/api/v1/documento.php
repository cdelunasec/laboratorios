<?php
require '/var/www/lab/lab.php';

require_login_api();

$d = query('SELECT id, titulo, contenido FROM documentos WHERE id = ?', [$_GET['id'] ?? 0])->fetch();
if (!$d) {
    json_response(['error' => 'No encontrado'], 404);
}
json_response($d);
