<?php
require '/var/www/lab/lab.php';

$me = require_login();
$pedidos = query('SELECT id, producto, total FROM pedidos WHERE user_id = ? ORDER BY id', [$me['id']])->fetchAll();

lab_header('Tus pedidos.');
?>
    <table class="data">
      <tr><th>Pedido</th><th>Producto</th><th>Total</th></tr>
<?php foreach ($pedidos as $p): ?>
      <tr><td><a href="/api/pedido.php?id=<?= e($p['id']) ?>">#<?= e($p['id']) ?></a></td><td><?= e($p['producto']) ?></td><td>RD$ <?= e($p['total']) ?></td></tr>
<?php endforeach; ?>
    </table>
<?php
lab_footer();
