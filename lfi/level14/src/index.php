<?php
require '/var/www/lab/lab.php';

lab_header('14', 'ReportesAPI', 'Genera reportes de ventas al momento.', 'el cuerpo de la petición');
lab_nav(['?report=reportes/diario.php' => 'Diario', '?report=reportes/mensual.php' => 'Mensual']);

$report = $_GET['report'] ?? 'reportes/diario.php';

$blocked = '#\.\./|^/|data:|filter|file://|zip://|phar://|expect://|https?://|ftp://#i';
if (preg_match($blocked, $report)) {
    lab_alert('err', 'Reporte no permitido.');
} else {
    include $report;
}

lab_footer('consigue ejecución de comandos, lista / y lee el archivo /flag_*.txt.');
