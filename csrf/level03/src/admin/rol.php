<?php
require '/var/www/lab/lab.php';

exigir_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Usa POST.');
}

// Revisa el token... cuando viene.
if (isset($_POST['token']) && !hash_equals(csrf_token('admin'), (string) $_POST['token'])) {
    responder(false, 'Token anti-CSRF inválido.');
}

$u = (string) ($_POST['usuario'] ?? '');
$rol = (string) ($_POST['rol'] ?? '');
responder(cambiar_rol($u, $rol), "Rol de $u: $rol");
