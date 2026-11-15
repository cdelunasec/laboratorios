<?php
require '/var/www/lab/lab.php';

$q = quien();
$autorizado = rol_de($q) === 'admin';
if (!$autorizado) {
    http_response_code(403);
}

lab_header('Panel de administración');

if (!$autorizado) {
    lab_alert('err', 'Necesitas el rol "admin" para ver el panel.' . ($q ? ' Tu rol es: ' . rol_de($q) . '.' : ' Entra primero con tu cuenta.'));
} else {
    echo '    <div class="alert ok">Panel de administración. Flag: <span class="flag">' . e(trim((string) file_get_contents('/flag.txt'))) . "</span></div>\n";
}
lab_footer();
