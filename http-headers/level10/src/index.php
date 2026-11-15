<?php
require '/var/www/lab/lab.php';

// El modo debug se activa por cabecera "para que soporte no toque el servidor".
$debug = strtolower(trim($_SERVER['HTTP_X_DEBUG_MODE'] ?? ''));
$debug = in_array($debug, ['on', '1', 'true', 'enabled', 'yes'], true);
header('X-Debug-Mode: ' . ($debug ? 'on' : 'off'));

lab_header('10', 'Clínica San Rafael', 'Citas médicas en línea.', 'lee las cabeceras de la respuesta');
?>
    <p>Agenda tu cita con medicina general, pediatría o ginecología.</p>
    <table class="data">
      <tr><th>Especialidad</th><th>Próxima cita disponible</th></tr>
      <tr><td>Medicina general</td><td>Lunes 8:30 am</td></tr>
      <tr><td>Pediatría</td><td>Martes 10:00 am</td></tr>
    </table>
<?php
if ($debug) {
    $config = [
        'db_host' => 'db.interno',
        'db_user' => 'citas_app',
        'smtp' => 'mail.interno:25',
        'api_secret' => lab_flag(),
    ];
    echo "    <h2>Debug</h2>\n    <pre>" . e(print_r($config, true)) . "</pre>\n";
}

lab_footer('consigue el secreto de la API de la clínica.');
