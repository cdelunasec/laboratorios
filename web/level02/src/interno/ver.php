<?php
require '/var/www/lib/app.php';

exigir_oficina();

// Nada de salirse de la carpeta de tickets.
$archivo = str_replace('../', '', (string) ($_GET['archivo'] ?? ''));

cabecera('Visor de tickets', '', false);
echo '  <section class="ld"><div class="ticket-head"><span class="ruta">' . e($archivo) . "</span></div>\n<pre>";
if ($archivo === '' || !@include SOPORTE . '/tickets/' . $archivo) {
    echo 'No se pudo abrir el archivo.';
}
echo "</pre>\n<p style=\"margin-top: 18px\"><a class=\"btn ghost\" href=\"/interno/\">← Volver a la consola</a></p></section>\n";
lab_footer();
