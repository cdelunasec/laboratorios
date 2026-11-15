<?php
require '/var/www/lab/lab.php';

lab_header('01', 'Recetario Criollo', 'Recetas dominicanas, una página a la vez.', 'sin filtros');
lab_nav(['?file=pages/inicio.php' => 'Inicio', '?file=pages/sancocho.php' => 'Sancocho', '?file=pages/mangu.php' => 'Mangú']);

$file = $_GET['file'] ?? 'pages/inicio.php';
include $file;

lab_footer('lee el archivo /flag.txt del servidor.');
