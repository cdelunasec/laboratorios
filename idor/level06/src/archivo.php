<?php
require '/var/www/lab/lab.php';

require_login();

// Sin comprobar dueño: "total, nadie conoce el UUID de otro".
$a = query('SELECT nombre, contenido FROM archivos WHERE uuid = ?', [$_GET['id'] ?? ''])->fetch();

lab_header('Archivo');
if (!$a) {
    lab_alert('err', 'Archivo no encontrado.');
} else {
    echo '    <h2>' . e($a['nombre']) . "</h2>\n    <pre>" . e($a['contenido']) . "</pre>\n";
}
lab_footer();
