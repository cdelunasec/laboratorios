<?php
require '/var/www/lab/lab.php';

$me = require_login_api();

$x = query('SELECT * FROM expedientes WHERE id = ?', [$_GET['id'] ?? 0])->fetch();
if (!$x) {
    json_response(['error' => 'No encontrado'], 404);
}
if ((int) $x['user_id'] !== (int) $me['id']) {
    json_response(['error' => 'Ese expediente no es tuyo.'], 403);
}
json_response(['id' => $x['id'], 'diagnostico' => $x['diagnostico'], 'notas' => $x['notas']]);
