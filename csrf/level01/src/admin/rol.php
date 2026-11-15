<?php
require '/var/www/lab/lab.php';

exigir_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(false, 'Usa POST.');
}

// Sin ninguna protección contra peticiones de otros sitios.
$u = (string) ($_POST['usuario'] ?? '');
$rol = (string) ($_POST['rol'] ?? '');
responder(cambiar_rol($u, $rol), "Rol de $u: $rol");
