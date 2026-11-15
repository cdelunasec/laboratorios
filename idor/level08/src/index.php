<?php
require '/var/www/lab/lab.php';

$me = require_login();
$ns = query('SELECT id, titulo FROM notas WHERE user_id = ? ORDER BY id', [$me['id']])->fetchAll();

lab_header('Tus notas.');
?>
    <ul>
<?php foreach ($ns as $n): ?>
      <li><a href="/nota.php?id=<?= e($n['id']) ?>"><?= e($n['titulo']) ?></a></li>
<?php endforeach; ?>
    </ul>
<?php
lab_footer();
