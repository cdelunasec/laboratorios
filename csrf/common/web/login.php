<?php
require '/var/www/lab/lab.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = (string) ($_POST['usuario'] ?? '');
    $datos = usuarios()[$u] ?? null;
    if ($u !== 'admin' && $datos && password_verify((string) ($_POST['clave'] ?? ''), $datos['clave'])) {
        session_regenerate_id(true);
        $_SESSION['usuario'] = $u;
        header('Location: /');
        exit;
    }
    $error = 'Usuario o contraseña incorrectos.';
}

lab_header('Iniciar sesión. Tu cuenta: carlos / carlos123');
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
<?php
lab_footer();
