<?php
require '/var/www/lab/lab.php';

lab_header('11', 'Disquería Merengue', 'Vinilos y CDs de merengue y bachata clásica.', 'byte nulo (PHP viejo simulado)');
lab_nav(['?page=catalogo' => 'Catálogo', '?page=ofertas' => 'Ofertas']);

$page = $_GET['page'] ?? 'catalogo';
$path = 'pages/' . $page . '.php';

// SIMULACIÓN de PHP < 5.3.4: aquellas versiones cortaban la ruta en el primer byte nulo.
// PHP moderno ya no lo hace (lanza error), así que el lab imita el comportamiento viejo.
$nul = strpos($path, "\0");
if ($nul !== false) {
    $path = substr($path, 0, $nul);
}

include $path;

lab_footer('lee el archivo /flag.txt del servidor.');
