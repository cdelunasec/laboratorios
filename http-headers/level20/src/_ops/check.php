<?php
require '/var/www/lab/lab.php';
require __DIR__ . '/_red.php';

ops_exigir_red_interna();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    lab_json(['error' => 'Solo POST.'], 405);
}

$host = $_SERVER['HTTP_X_CHECK_HOST'] ?? '';
if ($host === '') {
    lab_json(['error' => 'Falta la cabecera X-Check-Host.'], 400);
}

// "Sanitización": fuera los caracteres peligrosos de siempre.
if (preg_match('/[\s;&|`<>\'"\\\\]/', $host)) {
    lab_json(['error' => 'Caracteres no permitidos en X-Check-Host.'], 400);
}

exec("timeout 10 getent hosts $host > /dev/null 2>&1");

// Nunca decimos qué pasó: solo que se revisó.
lab_json(['revisado' => true]);
