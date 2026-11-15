<?php
require '/var/www/lab/lab.php';

exigir_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Usa POST.');
}

// Ahora sí compara el host del Referer... si es que viene.
$ref = (string) ($_SERVER['HTTP_REFERER'] ?? '');
if ($ref !== '' && strtolower((string) parse_url($ref, PHP_URL_HOST)) !== lab_host()) {
    responder(false, 'Petición rechazada: el Referer es de otro sitio.');
}

$u = (string) ($_POST['usuario'] ?? '');
$rol = (string) ($_POST['rol'] ?? '');
responder(cambiar_rol($u, $rol), "Rol de $u: $rol");
