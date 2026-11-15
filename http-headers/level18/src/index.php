<?php
require '/var/www/lab/lab.php';

// Auditoría: resuelve el nombre de la IP del visitante (detrás del proxy) y
// lo guarda... en ningún lado. El resultado nunca se muestra.
$ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'])[0]);
if (preg_match('/^\d{1,3}(\.\d{1,3}){3}/', $ip)) {
    exec("timeout 10 getent hosts $ip > /dev/null 2>&1");
}

lab_header('18', 'Aerolínea Caribeña', 'Vuelos nacionales e internacionales.', 'a ciegas');
?>
    <table class="data">
      <tr><th>Vuelo</th><th>Destino</th><th>Salida</th></tr>
      <tr><td>AC 101</td><td>Punta Cana</td><td>08:15</td></tr>
      <tr><td>AC 220</td><td>San Juan</td><td>11:40</td></tr>
      <tr><td>AC 305</td><td>Miami</td><td>16:05</td></tr>
    </table>
    <p class="dim">Por tu seguridad, todas las visitas quedan auditadas por IP.</p>
<?php
lab_footer('ejecuta comandos en el servidor y lee el flag (/flag_XXXX.txt). La app no te devuelve nada.');
