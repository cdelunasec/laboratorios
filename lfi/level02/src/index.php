<?php
require '/var/www/lab/lab.php';

lab_header('02', 'Biblioteca Digital', 'Documentos públicos de la comunidad.', 'rutas absolutas bloqueadas');
lab_nav(['?file=docs/bienvenida.php' => 'Inicio', '?file=docs/horario.php' => 'Horario', '?file=docs/reglamento.php' => 'Reglamento']);

$file = $_GET['file'] ?? 'docs/bienvenida.php';

if (str_starts_with($file, '/')) {
    lab_alert('err', 'Por seguridad no se permiten rutas absolutas.');
} else {
    include $file;
}

lab_footer('lee el archivo /flag.txt del servidor.');
