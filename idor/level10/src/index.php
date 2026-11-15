<?php
require '/var/www/lab/lab.php';

$me = require_login();

$archivo = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rows = query('SELECT fecha, concepto, monto FROM movimientos WHERE user_id = ? ORDER BY fecha', [$me['id']])->fetchAll();
    $csv = "fecha,concepto,monto\n";
    foreach ($rows as $r) {
        $csv .= "{$r['fecha']},{$r['concepto']},{$r['monto']}\n";
    }
    $archivo = 'export_' . $me['id'] . '_' . date('Ymd') . '.csv';
    file_put_contents('/var/www/html/exports/' . $archivo, $csv);
    query('INSERT INTO exportaciones (user_id, fecha) VALUES (?, ?)', [$me['id'], date('Y-m-d')]);
}

$hist = query('SELECT e.fecha, u.full_name FROM exportaciones e JOIN users u ON u.id = e.user_id ORDER BY e.id DESC LIMIT 10')->fetchAll();

lab_header('Tu cuenta de ahorros.');
if ($archivo) {
    echo '    <div class="alert ok">Exportación lista: <a href="/exports/' . e($archivo) . '">/exports/' . e($archivo) . "</a></div>\n";
}
?>
    <form method="post" class="stack">
      <button>Exportar mis movimientos (CSV)</button>
    </form>
    <h2>Últimas exportaciones de la cooperativa</h2>
    <table class="data">
      <tr><th>Fecha</th><th>Socio</th></tr>
<?php foreach ($hist as $h): ?>
      <tr><td><?= e($h['fecha']) ?></td><td><?= e($h['full_name']) ?></td></tr>
<?php endforeach; ?>
    </table>
<?php
lab_footer();
