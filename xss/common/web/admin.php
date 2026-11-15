<?php
require '/var/www/lab/lab.php';

if (!es_admin()) {
    http_response_code(403);
    lab_header('Panel de administración');
    lab_alert('err', 'Solo el administrador puede ver esta página.');
    lab_footer();
    exit;
}

lab_header('Panel de administración');
echo '    <div class="alert ok">Bienvenido, admin. Flag: <span class="flag">' . e(trim((string) file_get_contents('/flag.txt'))) . "</span></div>\n";
lab_footer();
