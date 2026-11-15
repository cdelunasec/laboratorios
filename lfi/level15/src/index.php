<?php
require '/var/www/lab/lab.php';

lab_header('15', 'Hotel Bávaro', 'Todo incluido frente a la playa.', 'la bitácora del servidor');
lab_nav(['?page=inicio.php' => 'Inicio', '?page=habitaciones.php' => 'Habitaciones']);

$page = $_GET['page'] ?? 'inicio.php';

if (preg_match('#://|data:#i', $page)) {
    lab_alert('err', 'Wrappers no permitidos.');
} else {
    include 'pages/' . $page;
}

lab_footer('consigue ejecución de comandos, lista / y lee el archivo /flag_*.txt.');
