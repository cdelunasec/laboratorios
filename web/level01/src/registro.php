<?php
require '/var/www/lib/app.php';

$error = null;
$nombre = $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = strtolower(trim((string) ($_POST['usuario'] ?? '')));
    $email = trim((string) ($_POST['email'] ?? ''));
    $clave = (string) ($_POST['clave'] ?? '');
    if (!preg_match('/^[a-z0-9_]{3,20}$/', $nombre)) {
        $error = 'El usuario debe tener de 3 a 20 letras, números o guion bajo.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Correo inválido.';
    } elseif (strlen($clave) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif (buscar_usuario($nombre)) {
        $error = 'Ese usuario ya existe.';
    } else {
        $u = usuarios();
        $id = max(array_keys($u)) + 1;
        $u[$id] = ['id' => $id, 'usuario' => $nombre, 'email' => $email, 'rol' => 'cliente',
                   'clave' => password_hash($clave, PASSWORD_DEFAULT), 'creado' => date('Y-m-d'), 'reset' => null];
        guardar_usuarios($u);
        session_regenerate_id(true);
        $_SESSION['uid'] = $id;
        lab_redirect('/cuenta.php');
    }
}

cabecera('Crea tu cuenta', 'Y compra más rápido la próxima vez.', true);
if ($error) {
    lab_alert('err', $error);
}
?>
  <div class="caja ld">
    <form class="form" method="post">
      <div class="campo"><label for="u">Usuario</label><input id="u" type="text" name="usuario" value="<?= e($nombre) ?>" autocomplete="username"></div>
      <div class="campo"><label for="m">Correo</label><input id="m" type="text" name="email" value="<?= e($email) ?>" autocomplete="email"></div>
      <div class="campo"><label for="c">Contraseña</label><input id="c" type="password" name="clave" autocomplete="new-password"></div>
      <button>Crear cuenta</button>
    </form>
    <p class="extra">¿Ya tienes cuenta? <a href="/login.php">Entra aquí</a>.</p>
  </div>
<?php
lab_footer();
