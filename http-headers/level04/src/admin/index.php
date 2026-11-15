<?php
require '/var/www/lab/lab.php';

lab_header('04', 'Colmado Doña Tata · Caja', 'Solo red interna.', 'el proxy no es el único que habla');

// Aprendieron la lección con X-Forwarded-For... pero la función de "IP real"
// la copiaron de un foro y revisa otras cabeceras.
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    lab_alert('err', 'X-Forwarded-For no se acepta en este servidor. Intento registrado.');
    lab_footer('abre la caja.');
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'];
foreach (['HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_REAL_IP', 'HTTP_TRUE_CLIENT_IP', 'HTTP_X_ORIGINATING_IP'] as $h) {
    if (!empty($_SERVER[$h])) {
        $ip = trim($_SERVER[$h]);
        break;
    }
}

if (preg_match('/^10\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $ip)) {
    lab_alert('ok', "Terminal interno $ip.");
    lab_show_flag('Combinación de la caja');
} else {
    lab_alert('err', "La IP $ip no pertenece a la red interna 10.0.0.0/8.");
}

lab_footer('abre la caja.');
