<?php
require '/var/www/lab/lab.php';

lab_header('03', 'Ferretería El Clavo', 'Inventario y pedidos.', 'solo desde la oficina');
lab_nav(['/' => 'Inicio', '/admin/' => 'Administración']);
?>
    <p>Bienvenido. Los empleados gestionan el inventario desde el área de administración, que solo funciona dentro de la oficina.</p>
<?php
lab_footer('entra al área de administración.');
