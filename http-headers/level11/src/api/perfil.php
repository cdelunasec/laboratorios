<?php
require '/var/www/lab/lab.php';
require '/var/www/lab/jwt.php';

$p = bearer_payload();
if ($p === null) {
    header('WWW-Authenticate: Bearer');
    lab_json(['error' => 'Token ausente o inválido.'], 401);
}

lab_json(['usuario' => $p['usuario'] ?? null, 'rol' => $p['rol'] ?? null, 'plan' => 'mensual']);
