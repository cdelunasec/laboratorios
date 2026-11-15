<?php
require '/var/www/lab/lab.php';

lab_header('03', 'Hostal Caribe', 'Reserva tu habitación frente al mar.', 'directorio fijo');
lab_nav(['?lang=es.php' => 'Español', '?lang=en.php' => 'English']);

$lang = $_GET['lang'] ?? 'es.php';
include 'languages/' . $lang;

lab_footer('lee el archivo /flag.txt del servidor.');
