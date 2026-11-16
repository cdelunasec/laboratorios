<?php
require '/var/www/lib/app.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = buscar_usuario(strtolower(trim((string) ($_POST['usuario'] ?? ''))));
    if ($u && password_verify((string) ($_POST['clave'] ?? ''), $u['clave'])) {
        session_regenerate_id(true);
        $_SESSION['uid'] = $u['id'];
        lab_redirect($u['rol'] === 'admin' ? '/admin/' : '/cuenta.php');
    }
    $error = 'Usuario o contraseña incorrectos.';
}

cabecera('Entra a tu cuenta.');
if ($error) {
    lab_alert('err', $error);
}
?>
    <form class="stack" method="post">
      <label>Usuario</label>
      <input type="text" name="usuario">
      <label>Contraseña</label>
      <input type="password" name="clave">
      <button>Entrar</button>
    </form>
    <p class="muted"><a href="/olvide.php">¿Olvidaste tu contraseña?</a> · <a href="/registro.php">Crear cuenta</a></p>
<?php
lab_footer();
