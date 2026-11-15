<?php
require '/var/www/lab/lab.php';

$me = require_login();

lab_header('Hola, ' . $me['full_name'] . '.');
lab_nav(['/' => 'Inicio', '/cuenta.php' => 'Cambiar contraseña']);
?>
    <table class="data">
      <tr><th>Usuario</th><td><?= e($me['username']) ?></td></tr>
      <tr><th>Rol</th><td><?= e($me['role']) ?></td></tr>
      <tr><th>Mis notas privadas</th><td><?= e($me['notas']) ?></td></tr>
    </table>
<?php
lab_footer();
