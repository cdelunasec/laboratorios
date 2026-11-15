<?php
require '/var/www/lab/lab.php';

exigir_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Usa POST.');
}

// El token tiene que ser uno de los que emitió el sistema... de quien sea.
$token = (string) ($_POST['token'] ?? '');
if ($token === '' || !in_array($token, datos_leer('tokens'), true)) {
    responder(false, 'Token anti-CSRF inválido.');
}

$u = (string) ($_POST['usuario'] ?? '');
$rol = (string) ($_POST['rol'] ?? '');
responder(cambiar_rol($u, $rol), "Rol de $u: $rol");
