<?php
require '/var/www/lib/app.php';

exigir_oficina();

// Nada de salirse de la carpeta de tickets.
$archivo = str_replace('../', '', (string) ($_GET['archivo'] ?? ''));

cabecera('Visor de tickets.');
echo '<p class="muted">Archivo: <code>' . e($archivo) . "</code></p>\n<pre>";
if ($archivo === '' || !@include SOPORTE . '/tickets/' . $archivo) {
    echo 'No se pudo abrir el archivo.';
}
echo "</pre>\n<p><a href=\"/interno/\">← Volver</a></p>\n";
lab_footer();
