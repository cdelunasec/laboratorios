<?php
require '/var/www/lab/lab.php';

lab_header('13', 'MemeGen RD', 'Genera memes con plantillas criollas.', 'datos en línea');
lab_nav(['?tpl=plantillas/clasico.php' => 'Clásico', '?tpl=plantillas/tigueraje.php' => 'Tigueraje']);

$tpl = $_GET['tpl'] ?? 'plantillas/clasico.php';

$blocked = '#\.\./|^/|php://|file://|zip://|phar://|expect://|https?://|ftp://#i';
if (preg_match($blocked, $tpl)) {
    lab_alert('err', 'Plantilla no permitida.');
} else {
    include $tpl;
}

lab_footer('consigue ejecución de comandos, lista / y lee el archivo /flag_*.txt.');
