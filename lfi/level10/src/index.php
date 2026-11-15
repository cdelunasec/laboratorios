<?php
require_once '/var/www/lab/lab.php';

lab_header('10', 'FacturaFácil', 'Facturación para pequeños negocios.', 'credenciales en el código');
lab_nav(['?page=inicio' => 'Inicio', '?page=precios' => 'Precios', '/admin.php' => 'Admin']);

$page = $_GET['page'] ?? 'inicio';
include $page . '.php';

lab_footer('entra al panel de administración y lee la flag.');
