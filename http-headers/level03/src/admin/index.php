<?php
require '/var/www/lab/lab.php';

lab_header('03', 'Ferretería El Clavo · Admin', 'Área restringida a la red de la oficina.', 'solo desde la oficina');

// Detrás del balanceador la IP real llega en X-Forwarded-For... o eso creían.
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
$ip = trim(explode(',', $ip)[0]);

if ($ip === '127.0.0.1') {
    lab_alert('ok', 'Conexión desde la oficina.');
    lab_show_flag('Clave del inventario');
} else {
    lab_alert('err', "Acceso denegado para $ip. El área de administración solo acepta conexiones locales (127.0.0.1).");
}

lab_footer('entra al área de administración.');
