<?php
require '/var/www/lab/lab.php';

$me = require_login();
$fs = query('SELECT id, ref, periodo, monto FROM facturas WHERE user_id = ? ORDER BY id', [$me['id']])->fetchAll();

lab_header('Tus facturas de luz.');
?>
    <table class="data">
      <tr><th>N.º</th><th>Período</th><th>Monto</th></tr>
<?php foreach ($fs as $f): ?>
      <tr><td><a href="/factura.php?f=<?= e($f['ref']) ?>"><?= e($f['id']) ?></a></td><td><?= e($f['periodo']) ?></td><td>RD$ <?= e($f['monto']) ?></td></tr>
<?php endforeach; ?>
    </table>
<?php
lab_footer();
