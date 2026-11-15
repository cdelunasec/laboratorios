<?php
require '/var/www/lab/lab.php';

require_login();

// Muestra el perfil completo del id que venga en la URL.
$u = query('SELECT * FROM users WHERE id = ?', [$_GET['id'] ?? 0])->fetch();

lab_header('Tu perfil');
if (!$u) {
    lab_alert('err', 'Ese perfil no existe.');
} else {
?>
    <table class="data">
      <tr><th>Usuario</th><td><?= e($u['username']) ?></td></tr>
      <tr><th>Nombre</th><td><?= e($u['full_name']) ?></td></tr>
      <tr><th>Correo</th><td><?= e($u['email']) ?></td></tr>
      <tr><th>Notas privadas</th><td><?= e($u['notas']) ?></td></tr>
    </table>
<?php
}
lab_footer();
