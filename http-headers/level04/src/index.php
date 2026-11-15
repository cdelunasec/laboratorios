<?php
require '/var/www/lab/lab.php';

lab_header('04', 'Colmado Doña Tata', 'Pedidos a domicilio.', 'el proxy no es el único que habla');
lab_nav(['/' => 'Inicio', '/admin/' => 'Caja']);
?>
    <p>La caja solo se abre desde la red interna del colmado (10.0.0.0/8).</p>
<?php
lab_footer('abre la caja.');
