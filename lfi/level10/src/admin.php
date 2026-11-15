<?php
require_once '/var/www/lab/lab.php';
require_once 'config.php';

lab_header('10', 'FacturaFácil · Admin', 'Acceso restringido.', 'credenciales en el código');
lab_nav(['/' => 'Volver']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ok = hash_equals($admin_user, $_POST['user'] ?? '') && hash_equals($admin_pass, $_POST['pass'] ?? '');
    if ($ok) {
        lab_alert('ok', 'Bienvenido, admin. Flag: ' . trim(file_get_contents('/opt/vault/flag.txt')));
    } else {
        lab_alert('err', 'Usuario o contraseña incorrectos.');
    }
}
?>
    <form method="post" class="upload">
      <label for="user">Usuario</label>
      <input type="text" name="user" id="user" required>
      <label for="pass">Contraseña</label>
      <input type="text" name="pass" id="pass" required>
      <button type="submit">Entrar</button>
    </form>
<?php
lab_footer('entra al panel de administración y lee la flag.');
