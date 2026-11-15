<?php
require '/var/www/lab/lab.php';

exigir_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Usa POST.');
}

// Lee el cuerpo como JSON sin mirar el Content-Type.
$d = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($d)) {
    responder(false, 'El cuerpo tiene que ser JSON.');
}

$u = (string) ($d['usuario'] ?? '');
$rol = (string) ($d['rol'] ?? '');
responder(cambiar_rol($u, $rol), "Rol de $u: $rol");
