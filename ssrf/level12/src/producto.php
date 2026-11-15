<?php
require '/var/www/lab/lab.php';

$productos = [1 => ['Abanico de pie', 2450], 2 => ['Greca de 6 tazas', 890], 3 => ['Licuadora', 3200]];
$p = $productos[(int) ($_GET['id'] ?? 1)] ?? $productos[1];

lab_header('12', 'Tienda Local', 'Producto', 'jefe final');
echo '    <h2>' . e($p[0]) . "</h2>\n    <p>RD$ " . e($p[1]) . "</p>\n";
lab_footer('consigue las credenciales del rol de la instancia.');
