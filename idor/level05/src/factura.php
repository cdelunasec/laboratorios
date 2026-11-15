<?php
require '/var/www/lab/lab.php';

require_login();

$f = query('SELECT f.*, u.full_name FROM facturas f JOIN users u ON u.id = f.user_id WHERE f.ref = ?', [$_GET['f'] ?? ''])->fetch();

lab_header('Factura');
if (!$f) {
    lab_alert('err', 'Factura no encontrada.');
} else {
?>
    <table class="data">
      <tr><th>Factura</th><td><?= e($f['id']) ?></td></tr>
      <tr><th>Cliente</th><td><?= e($f['full_name']) ?></td></tr>
      <tr><th>Período</th><td><?= e($f['periodo']) ?></td></tr>
      <tr><th>Monto</th><td>RD$ <?= e($f['monto']) ?></td></tr>
      <tr><th>Detalle</th><td><?= e($f['detalle']) ?></td></tr>
    </table>
<?php
}
lab_footer();
