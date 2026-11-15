<?php
require '/var/www/lab/lab.php';

exigir_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Usa POST.');
}

// Double submit: el campo token tiene que ser igual a la cookie csrf.
$campo = (string) ($_POST['token'] ?? '');
$cookie = (string) ($_COOKIE['csrf'] ?? '');
if ($campo === '' || !hash_equals($cookie, $campo)) {
    responder(false, 'Token anti-CSRF inválido (no coincide con la cookie csrf).');
}

$u = (string) ($_POST['usuario'] ?? '');
$rol = (string) ($_POST['rol'] ?? '');
responder(cambiar_rol($u, $rol), "Rol de $u: $rol");
