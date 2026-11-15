<?php
require '/var/www/lab/lab.php';

$me = require_login();

lab_header('Panel de ' . $me['full_name'] . '.');
?>
    <div id="panel"></div>
    <script>window.YO = <?= json_encode(['usuario' => $me['username'], 'rol' => $me['role']]) ?>;</script>
    <script src="/assets/panel.js"></script>
<?php
lab_footer();
