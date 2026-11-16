<?php
// API de perfiles. La usa /cuenta.php para pintar los datos del cliente.
require '/var/www/lib/app.php';

if (!yo()) {
    lab_json(['error' => 'Necesitas iniciar sesión'], 401);
}

$id = (int) ($_GET['id'] ?? 0);
$u = usuarios()[$id] ?? null;
if (!$u) {
    lab_json(['error' => 'No existe ese usuario'], 404);
}

unset($u['clave']);
lab_json($u);
