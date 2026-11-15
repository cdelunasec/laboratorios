<?php
require '/var/www/lab/lab.php';

// El widget de estado de la consola de operaciones consulta este sitio desde el navegador.
header('Access-Control-Allow-Origin: http://ops.redcaribe.local');
header('Access-Control-Allow-Credentials: true');

lab_header('20', 'Red Caribe · Estado', 'Estado de los servicios de internet.', 'jefe final');
?>
    <table class="data">
      <tr><th>Servicio</th><th>Estado</th></tr>
      <tr><td>Fibra Santo Domingo</td><td>operativo</td></tr>
      <tr><td>Fibra Santiago</td><td>operativo</td></tr>
      <tr><td>Telefonía IP</td><td>mantenimiento</td></tr>
    </table>
<?php
lab_footer('ejecuta comandos en el servidor y lee el flag (/flag_XXXX.txt).');
