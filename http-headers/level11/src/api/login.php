<?php
require '/var/www/lab/lab.php';
require '/var/www/lab/jwt.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    lab_json(['error' => 'Usa POST con usuario y clave.'], 405);
}

$u = (string) ($_POST['usuario'] ?? '');
$c = (string) ($_POST['clave'] ?? '');

// Solo existe la cuenta de prueba; la del dueño no entra por aquí.
if ($u === 'invitado' && $c === 'invitado') {
    lab_json(['token' => jwt_sign(['usuario' => 'invitado', 'rol' => 'socio', 'iat' => time()])]);
}

lab_json(['error' => 'Credenciales incorrectas.'], 401);
