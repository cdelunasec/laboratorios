<?php
require '/var/www/lab/lab.php';

$me = require_login();

lab_header('Cambiar contraseña');
lab_nav(['/' => 'Inicio', '/cuenta.php' => 'Cambiar contraseña']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nueva = (string) ($_POST['nueva'] ?? '');
    if (strlen($nueva) < 6) {
        lab_alert('err', 'La contraseña debe tener al menos 6 caracteres.');
    } else {
        // Cambia la contraseña de la cuenta que diga el formulario.
        $n = query('UPDATE users SET password = ? WHERE id = ?', [password_hash($nueva, PASSWORD_DEFAULT), $_POST['id'] ?? 0])->rowCount();
        lab_alert($n ? 'ok' : 'err', $n ? 'Contraseña actualizada.' : 'No se pudo actualizar.');
    }
}
?>
    <form method="post" class="stack">
      <input type="hidden" name="id" value="<?= e($me['id']) ?>">
      <label>Nueva contraseña</label>
      <input type="password" name="nueva" required>
      <button>Guardar</button>
    </form>
<?php
lab_footer();
