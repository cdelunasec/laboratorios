<?php
require '/var/www/lab/lab.php';

// API v1 (app móvil vieja). Antes del control de acceso por dueño.
require_login_api();

$x = query('SELECT * FROM expedientes WHERE id = ?', [$_GET['id'] ?? 0])->fetch();
if (!$x) {
    json_response(['error' => 'No encontrado'], 404);
}
json_response(['id' => $x['id'], 'diagnostico' => $x['diagnostico'], 'notas' => $x['notas']]);
