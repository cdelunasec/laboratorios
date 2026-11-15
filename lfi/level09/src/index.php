<?php
require '/var/www/lab/lab.php';

lab_header('09', 'Clínica Dental Sonrisa', 'Tu sonrisa en buenas manos.', 'leer sin ejecutar');
lab_nav(['?page=inicio' => 'Inicio', '?page=servicios' => 'Servicios']);

$page = $_GET['page'] ?? 'inicio';
include $page . '.php';

lab_footer('la flag está escondida en el código fuente de la aplicación.');
