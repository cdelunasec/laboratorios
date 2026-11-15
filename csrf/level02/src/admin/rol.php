<?php
require '/var/www/lab/lab.php';

exigir_admin();

// Acción que cambia datos... por GET, desde un enlace.
$u = (string) ($_GET['usuario'] ?? '');
$rol = (string) ($_GET['rol'] ?? '');
responder(cambiar_rol($u, $rol), "Rol de $u: $rol");
