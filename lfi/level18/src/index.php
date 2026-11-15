<?php
require '/var/www/lab/lab.php';

lab_header('18', 'Widget del Clima', 'El pronóstico del tiempo para tu web.', 'inclusión remota');
lab_nav(['?widget=widgets/hoy' => 'Hoy', '?widget=widgets/semana' => 'Semana']);

$widget = $_GET['widget'] ?? 'widgets/hoy';

if (preg_match('#\.\./|^/|php://|data:|file://|zip://|phar://|expect://#i', $widget)) {
    lab_alert('err', 'Widget no permitido.');
} else {
    include $widget . '.php';
}

lab_footer('consigue ejecución de comandos, lista / y lee el archivo /flag_*.txt.');
