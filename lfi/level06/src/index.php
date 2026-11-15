<?php
require '/var/www/lab/lab.php';

lab_header('06', 'Escuela de Idiomas', 'Inglés, francés e italiano para todas las edades.', 'ruta aprobada');
lab_nav(['?lang=./languages/es.php' => 'Español', '?lang=./languages/fr.php' => 'Français']);

$lang = $_GET['lang'] ?? './languages/es.php';

if (!preg_match('#^\./languages/#', $lang)) {
    lab_alert('err', 'Solo se permiten archivos dentro de ./languages/');
} else {
    include $lang;
}

lab_footer('lee el archivo /flag.txt del servidor.');
