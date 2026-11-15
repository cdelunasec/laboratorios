<?php
require '/var/www/lab/lab.php';

lab_header('12', 'Tienda Local', 'Ayuda', 'jefe final');
?>
    <h2>Preguntas frecuentes</h2>
    <p><b>¿Dónde está alojada la tienda?</b><br>En una instancia de NubeRD.</p>
    <p><b>¿Por qué mi enlace no funciona?</b><br>Solo aceptamos enlaces de <code>http://tienda.local/</code>. Si el producto se movió, seguimos la redirección.</p>
    <p class="dim">Nota para administradores: los metadatos de la instancia se consultan en <code>metadata.nube.local:8775</code> (solo desde la propia instancia).</p>
<?php
lab_footer('consigue las credenciales del rol de la instancia.');
