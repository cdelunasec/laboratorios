<?php
require '/var/www/lab/lab.php';

$me = require_login();
$docs = query('SELECT id, titulo FROM documentos WHERE user_id = ? ORDER BY id', [$me['id']])->fetchAll();

lab_header('Tus documentos notariados.');
?>
    <table class="data">
      <tr><th>Documento</th></tr>
<?php foreach ($docs as $d): ?>
      <tr><td><a href="/ver.php?ref=<?= e(base64_encode('documento-' . $d['id'])) ?>"><?= e($d['titulo']) ?></a></td></tr>
<?php endforeach; ?>
    </table>
    <p class="dim">Las referencias de los documentos son opacas para proteger tu privacidad.</p>
<?php
lab_footer();
