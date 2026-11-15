<?php
require '/var/www/lab/lab.php';

exigir_admin();

// Las peticiones POST llevan token. Las demás "solo leen"... en teoría.
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && !hash_equals(csrf_token('admin'), (string) ($_POST['token'] ?? ''))) {
    responder(false, 'Token anti-CSRF inválido.');
}

$u = (string) ($_REQUEST['usuario'] ?? '');
$rol = (string) ($_REQUEST['rol'] ?? '');
responder(cambiar_rol($u, $rol), "Rol de $u: $rol");
