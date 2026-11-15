<?php
require '/var/www/lab/lab.php';

exigir_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Usa POST.');
}

// "Viene de nuestro sitio" si el Referer menciona nuestro dominio.
$ref = (string) ($_SERVER['HTTP_REFERER'] ?? '');
if (!str_contains($ref, lab_host())) {
    responder(false, 'Petición rechazada: no viene de ' . lab_host() . '.');
}

$u = (string) ($_POST['usuario'] ?? '');
$rol = (string) ($_POST['rol'] ?? '');
responder(cambiar_rol($u, $rol), "Rol de $u: $rol");
