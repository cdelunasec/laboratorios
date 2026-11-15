<?php
require '/var/www/lab/lab.php';

lab_header('12', 'Cooperativa Cibao Unido', 'Documentos para socios.', 'descarga por partes');
?>
    <table class="data">
      <tr><th>Documento</th><th></th></tr>
      <tr><td>Informe anual de la cooperativa</td><td><a href="/descargar.php">descargar</a></td></tr>
    </table>
    <p class="dim">La última línea del informe trae el código de la asamblea.</p>
<?php
lab_footer('lee la última línea del informe anual.');
