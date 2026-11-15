<?php
require '/var/www/lab/lab.php';

require_login_api();

// Búsqueda pública de ciudadanos. Los ids internos nunca se exponen... casi.
$q = '%' . ($_GET['q'] ?? '') . '%';
$rows = query('SELECT id, uuid, username, full_name FROM users WHERE username LIKE ? OR full_name LIKE ? ORDER BY username', [$q, $q])->fetchAll();

json_response(array_map(fn ($u) => [
    'uuid' => $u['uuid'],
    'usuario' => $u['username'],
    'nombre' => $u['full_name'],
    'avatar' => '/avatars/' . $u['id'] . '.svg',
], $rows));
