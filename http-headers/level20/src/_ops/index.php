<?php
require '/var/www/lab/lab.php';
require __DIR__ . '/_red.php';

ops_exigir_red_interna();

lab_header('20', 'Red Caribe · Ops', 'Consola de operaciones.', 'jefe final');
lab_alert('ok', 'Bienvenido a la consola de operaciones.');
?>
    <table class="data">
      <tr><th>Herramienta</th><th>Uso</th></tr>
      <tr><td><code>/check.php</code></td><td>Verifica que un host responde. Solo <code>POST</code>; el host va en la cabecera <code>X-Check-Host</code>.</td></tr>
    </table>
<?php
lab_footer('ejecuta comandos en el servidor y lee el flag (/flag_XXXX.txt).');
