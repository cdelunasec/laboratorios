<?php
require '/var/www/lab/lab.php';

$me = require_login();
$x = query('SELECT id FROM expedientes WHERE user_id = ?', [$me['id']])->fetch();

lab_header('Tu expediente médico.');
?>
    <pre id="exp">Cargando…</pre>
    <script src="/assets/app.js"></script>
    <script>cargarExpediente(<?= (int) $x['id'] ?>);</script>
<?php
lab_footer();
