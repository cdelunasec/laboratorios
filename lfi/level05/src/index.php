<?php
require '/var/www/lab/lab.php';

lab_header('05', 'Colmado Online', 'Lo que necesitas, sin salir de la casa.', 'filtro de una sola pasada');
lab_nav(['?page=ofertas.php' => 'Ofertas', '?page=delivery.php' => 'Delivery']);

$page = $_GET['page'] ?? 'ofertas.php';
// "Arreglo" de seguridad: quitamos los ../
$page = str_replace('../', '', $page);
include 'pages/' . $page;

lab_footer('lee el archivo /flag.txt del servidor.');
