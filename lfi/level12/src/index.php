<?php
require '/var/www/lab/lab.php';

lab_header('12', 'Mapas del Cibao', 'Rutas y mapas turísticos del norte.', 'sin barra ni puntos');
lab_nav(['?map=mapas/jarabacoa.php' => 'Jarabacoa', '?map=mapas/constanza.php' => 'Constanza']);

$map = $_GET['map'] ?? 'mapas/jarabacoa.php';

// Ahora sí recursivo: quitamos ../ hasta que no quede ninguno.
while (str_contains($map, '../')) {
    $map = str_replace('../', '', $map);
}

if (str_starts_with($map, '/')) {
    lab_alert('err', 'Por seguridad no se permiten rutas absolutas.');
} else {
    include $map;
}

lab_footer('lee el archivo /flag.txt del servidor.');
