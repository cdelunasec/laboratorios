<?php
require '/var/www/lab/lab.php';
require '/var/www/lab/jwt.php';

$p = bearer_payload();
if ($p === null) {
    header('WWW-Authenticate: Bearer');
    lab_json(['error' => 'Token ausente o inválido.'], 401);
}
if (($p['rol'] ?? '') !== 'admin') {
    lab_json(['error' => 'Solo el rol admin puede ver el panel.'], 403);
}

lab_json(['panel' => 'dueño', 'socios_activos' => 412, 'clave_maestra' => lab_flag()]);
