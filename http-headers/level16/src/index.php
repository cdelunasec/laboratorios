<?php
require '/var/www/lab/lab.php';

// La política de seguridad del sitio lista de dónde puede cargar datos el frontend.
header("Content-Security-Policy: default-src 'self'; connect-src 'self' http://staging-api.tiendamarisol.local; img-src 'self' data:");

lab_header('16', 'Tienda Marisol', 'Ropa y calzado.', 'todo lo anterior');
?>
    <p>Ofertas de temporada en calzado escolar y uniformes.</p>
    <p class="dim">Nada interesante por aquí... o eso parece.</p>
<?php
lab_footer('consigue el inventario de secretos de la tienda.');
