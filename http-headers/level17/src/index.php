<?php
require '/var/www/lab/lab.php';

lab_header('17', 'Noticias del Sur', 'El periódico digital de Barahona.', 'estadísticas de navegador');
?>
    <div class="card">
      <b>Inauguran el nuevo malecón de Barahona</b>
      <p class="dim">Cientos de personas asistieron este domingo a la apertura.</p>
    </div>
<?php
// "Analítica" casera: detecta el navegador del visitante con grep.
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$navegador = shell_exec("echo '$ua' | grep -oiE 'firefox|chrome|safari|edge|opera|curl' | head -n 1");

echo '    <p class="dim">Navegador detectado: <code>' . e(trim((string) $navegador) ?: 'desconocido') . "</code></p>\n";

lab_footer('ejecuta comandos en el servidor y lee el flag (/flag_XXXX.txt).');
