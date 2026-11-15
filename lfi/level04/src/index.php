<?php
require '/var/www/lab/lab.php';

lab_header('04', 'Taller El Primo', 'Mecánica general, desabolladura y pintura.', 'extensión forzada');
lab_nav(['?page=inicio' => 'Inicio', '?page=servicios' => 'Servicios', '?page=contacto' => 'Contacto']);

$page = $_GET['page'] ?? 'inicio';
include 'pages/' . $page . '.php';

lab_footer('encuentra el panel interno que dejaron olvidado y lee su flag.');
