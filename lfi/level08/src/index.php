<?php
require '/var/www/lab/lab.php';

lab_header('08', 'Galería de Arte', 'Exposiciones virtuales de artistas locales.', 'decodificado dos veces');
lab_nav(['?file=sala1.php' => 'Sala 1', '?file=sala2.php' => 'Sala 2']);

$file = $_GET['file'] ?? 'sala1.php';

if (str_contains($file, '..')) {
    lab_alert('err', 'Ataque detectado: path traversal.');
} else {
    // Por si algún enlace viejo viene codificado...
    $file = urldecode($file);
    include 'salas/' . $file;
}

lab_footer('lee el archivo /flag.txt del servidor.');
