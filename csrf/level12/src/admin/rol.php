<?php
require '/var/www/lab/lab.php';
require '/var/www/html/_cors.php';

cors();
exigir_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Usa POST.');
}

// Solo JSON de verdad (un formulario HTML no puede mandar este Content-Type).
if (!str_starts_with(strtolower((string) ($_SERVER['CONTENT_TYPE'] ?? '')), 'application/json')) {
    responder(false, 'Content-Type tiene que ser application/json.');
}
if (!hash_equals(csrf_token('admin'), (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''))) {
    responder(false, 'Falta la cabecera X-CSRF-Token o es inválida.');
}

$d = json_decode((string) file_get_contents('php://input'), true) ?: [];
$u = (string) ($d['usuario'] ?? '');
$rol = (string) ($d['rol'] ?? '');
responder(cambiar_rol($u, $rol), "Rol de $u: $rol");
