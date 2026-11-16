<?php
require '/var/www/lib/app.php';

$error = null;
$nombre = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = strtolower(trim((string) ($_POST['usuario'] ?? '')));
    $u = buscar_usuario($nombre);
    if ($u && password_verify((string) ($_POST['clave'] ?? ''), $u['clave'])) {
        session_regenerate_id(true);
        $_SESSION['uid'] = $u['id'];
        lab_redirect($u['rol'] === 'admin' ? '/admin/' : '/cuenta.php');
    }
    $error = 'Usuario o contraseña incorrectos.';
}

cabecera('Bienvenido de vuelta', 'Entra para ver tu cuenta y tus pedidos.', true);
if ($error) {
    lab_alert('err', $error);
}
?>
  <div class="caja ld">
    <form class="form" method="post">
      <div class="campo"><label for="u">Usuario</label><input id="u" type="text" name="usuario" value="<?= e($nombre) ?>" autocomplete="username"></div>
      <div class="campo"><label for="c">Contraseña</label><input id="c" type="password" name="clave" autocomplete="current-password"></div>
      <button>Entrar</button>
    </form>
    <p class="extra"><a href="/olvide.php">¿Olvidaste tu contraseña?</a> · <a href="/registro.php">Crear cuenta</a></p>
  </div>
<?php
lab_footer();
